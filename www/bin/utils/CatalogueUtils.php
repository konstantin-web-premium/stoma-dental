<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bin/catalogue/ProductItem.php");

class CatalogueUtils{
    public static $tech_pages_list = array( PAGE_INDEX, PAGE_LOGIN, PAGE_LOGOUT, PAGE_REGISTER, PAGE_ACCESS_DENIED );

    public function __construct(){
    }


// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * @param $label
     * @return array
     */
    public static function getCatalogueNode($label){
        $t_catalogue = TABLE_CATALOGUE;
        $t_meta = TABLE_META;

        $query_str = "SELECT *,".
                     "$t_catalogue.id as id ".
                     "FROM $t_catalogue ".
                     "LEFT JOIN $t_meta ON $t_catalogue.meta_id = $t_meta.id ".
                     "WHERE label='$label' ".
                     "LIMIT 1";
        $query = G::$db->query($query_str) or G::fatalError("CatalogueUtils::getCatalogueNode() : ".DATABASE_ERROR_MESSAGE);

        $node = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();

        $node["user_edited_data"] = PageUtils::parseItemUserData($node["created_data"], $node["last_edited_data"]);
        unset($node["created_data"], $node["last_edited_data"]);

        return $node;
    }


    /**
     * @param $id - product\category id
     * @param $data (validated) - ( key => value )
     * @return bool
     */
    public static function updateCatalogueNode($id, $data){
        $t_catalogue = TABLE_CATALOGUE;
        $t_meta = TABLE_META;
        unset($data["id"]);
        $data["last_edited_data"] = G::$user->data["id"] . "," . date("U");
        $data_serialized = G::serializeUpdate($data);

        $query_str = "UPDATE $t_catalogue ".
            "LEFT JOIN $t_meta ON $t_catalogue.meta_id = $t_meta.id ".
            "SET " . $data_serialized . " " .
            "WHERE $t_catalogue.id='$id'";

        G::$db->exec($query_str);
        $result = intval(G::$db->errorCode());
        if ($result != 0){
            $error_info = G::$db->errorInfo();
            $text_error = " DB >> " . $error_info[1] . " > " . $error_info[2];
            G::fatalError("CatalogueUtils::updateCatalogueNode() >> " . $text_error);
        }

        return !$result;
    }

    public static function findParentsOf($id){
        $t_catalogue = TABLE_CATALOGUE;
        $query_str = "SELECT id,children_id ".
            "FROM $t_catalogue " .
            "WHERE children_id REGEXP '^$id,|,$id,|,$id$|^$id$'";
        $query = G::$db->query($query_str);
        if (intval(G::$db->errorCode) > 0){
            $errorInfo = G::$db->errorInfo();
            G::fatalError(">>". $errorInfo[2]);
        }
        $parents = $query->fetchAll(PDO::FETCH_ASSOC);
        return $parents;
    }


    public static function deleteChildFromParent($child_id, $parent_id){
        $t_catalogue = TABLE_CATALOGUE;
        $query = G::$db->query("SELECT id,children_id FROM $t_catalogue WHERE id = '$parent_id' LIMIT 1");
        $parent_data = $query->fetchAll(PDO::FETCH_ASSOC);
        $parent_data = $parent_data[0];
        $parent_data["children_id"] = preg_replace("/^$child_id,|,$child_id$|^$child_id$/", "", $parent_data["children_id"]);
        $parent_data["children_id"] = preg_replace("/,$child_id,/", ",", $parent_data["children_id"]);
        self::updateCatalogueNode($parent_data["id"], $parent_data);
    }

    public static function addChildToParent($child_id, $parent_id){
        $t_catalogue = TABLE_CATALOGUE;
        $query_str = "UPDATE $t_catalogue " .
                    "SET children_id = " .
                        "IF(children_id='','$child_id',CONCAT_WS(',', children_id, '$child_id')) " .
                    "WHERE id = '$parent_id' LIMIT 1";
        $query = G::$db->exec($query_str);
        G::logMessage(" Add $child_id to $parent_id = $query " . print_r(G::$db->errorInfo(), true));
    }

    /**
     * @param $data (validated) - array( key => value )
     * @return bool
     */
    public static function createCatalogueNode($data, $parent_id){
        $t_catalogue = TABLE_CATALOGUE;
        $t_meta = TABLE_META;
        $data["created_data"] = G::$user->data["id"] . "," . date("U");
        $data_serialized = G::serializeInsert($data, $t_meta);

        $query_str = "INSERT INTO $t_meta " . $data_serialized;
        G::$db->exec($query_str) or G::fatalError("CatalogueUtils::createCatalogueNode() : META INSERT :".print_r(G::$db->errorInfo(), true));

        $data["meta_id"] = G::$db->lastInsertId();
        $data_serialized = G::serializeInsert($data, $t_catalogue);

        $query_str = "INSERT INTO $t_catalogue " . $data_serialized;
        G::$db->exec($query_str) or G::fatalError("CatalogueUtils::createCatalogueNode() : PAGES INSERT :".print_r(G::$db->errorInfo(), true));

        $new_node_id = G::$db->lastInsertId();
        if ($parent_id){
            self::addChildToParent($new_node_id, $parent_id);
        }

        //G::logMessage("createCatalogueNode >> meta_id = " . $data["meta_id"] . "; new id = " . G::$db->lastInsertId());

        return true;
    }

    public static function makeTree($data){
        foreach($data as $item){
            if ($item['label'] == CATALOGUE_ROOT_LABEL){
                $tree = $item;
                break;
            }
        }
        self::setChildren($tree, $data);

        return $tree;
    }

    public static function setChildren(&$parent, $arr){
        if (!isset($parent["children_id"]) || strlen($parent["children_id"]) == 0){
            unset($parent["children_id"]);
            return;
        }
        $children = preg_split("/\,/", $parent["children_id"]);
        foreach($children as $id){
            if (!isset($parent["children"])){
                $parent["children"] = array();
            }
            $parent["children"][] = $arr[$id];

            $i = count($parent["children"])-1;
            self::setChildren($parent["children"][$i], $arr);
        }
    }

    /**
     * @return array()
     */
    public static function getCategoryTree(){
        $data = self::getAllCategories();
        $tree = self::makeTree($data);

        return $tree;
    }

    public static function getAllCategories($key = "id"){
        $data = array();
        $t_cat = TABLE_CATALOGUE;
        $query = G::$db->query("SELECT * FROM $t_cat WHERE children_id IS NOT NULL OR children_id != ''") or G::fatalError("CatalogueUtils::getAllCategories() : " . print_r(G::$db->errorInfo(), true));
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $query->fetch()){
            $data[$row[$key]] = $row;
        }
        return $data;
    }

    public static function getProducts($childrenId, $length = 0, $page = 1){
        $length = $length < 0 ? 0 : $length;
        $page = $page < 1 ? 1 : $page;
        $t_catalogue = TABLE_CATALOGUE;
        $t_meta = TABLE_META;
        $data = array();
        if (is_array($childrenId)){
            $childrenId = implode(",", $childrenId);
        }

        $id_arr = explode(",", $childrenId);
        $items_limit = (isset($_SESSION["page_items_limit"]) ? $_SESSION["page_items_limit"] : Paginator::PAGE_ITEMS_LIMIT);
        if ($page > 1 || $length > 0){
            $id_arr = array_splice($id_arr, ($page-1) * $items_limit, ($length > 0 ? $length : null));
        }

        $childrenId = implode(",", $id_arr);

        $query_str = "SELECT *," .
            "$t_catalogue.id as id ".
            "FROM $t_catalogue ".
            "LEFT JOIN $t_meta ON $t_catalogue.meta_id = $t_meta.id " .
            "WHERE $t_catalogue.id IN ($childrenId)";
        $query = G::$db->query($query_str) or G::fatalError("CatalogueUtils::getProducts() : " . print_r(G::$db->errorInfo(), true));

        while($row = $query->fetch()){
            $pos = array_search($row["id"], $id_arr);
            $data[$pos] = new ProductItem($row);
        }

        return $data;
    }

    public static function getCountProducts($childrenId){
        $t_catalogue = TABLE_CATALOGUE;
        $query_str = "SELECT COUNT(id) ".
                "FROM $t_catalogue ".
                "WHERE id IN ($childrenId)";
        $query = G::$db->query($query_str) or G::fatalError("CatalogueUtils::getCountProducts : " . print_r(G::$db->errorInfo(), true));
        $data = $query->fetchAll(PDO::FETCH_NUM);

        return $data[0][0];
    }

    /**
     * Load titles of each path label
     */
    public static function getPathItems($path_arr){

        # TODO path items limit!

        $path_items = array();
        $t_cat = TABLE_CATALOGUE;
        $addr = "'".implode("','", $path_arr)."'";
        $query = G::$db->query("SELECT * FROM $t_cat WHERE label IN ($addr)");
        while($row = $query->fetch()){
            $key = array_search($row["label"], $path_arr);
            $path_items[$key] = $row;
        }

        if (count($path_arr) !== count($path_items)){

            # TODO redirect on 404
            // DEBUG
            G::fatalError("CatalogueUtils::getPathItems() : Some of items not found!");
            //
        }

        return $path_items;
    }

    public static function loadPropsData(){
        $data = array();
        $query = G::$db->query("SELECT * FROM " . TABLE_CATALOGUE_PROPS );
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}
?>