<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bin/catalogue/ProductItem.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/external/urlify-master/URLify.php");

class PageUtils{
    public static $tech_pages_list = array( PAGE_INDEX, PAGE_LOGIN, PAGE_ENTER, PAGE_LOGOUT, PAGE_REGISTER, PAGE_ACCESS_DENIED );

    public function __construct(){
    }


// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private static function isTechnicalPage($label){
        if (in_array($label, self::$tech_pages_list)){
            return true;
        }
        return false;
    }


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     *  ?????????? DELETE this method ???
     */
    public static function validateLabel($label){
        if (!preg_match(LABEL_REG_EXP ,$label)){
            return false;
        }
        return true;
    }

    /**
     *  Get all blocks as array with id-keys
     * @return array
     */
    public static function getBlocks(){
        $table_name = TABLE_PAGEBLOCKS;

        $query = G::$db->query("SELECT * FROM $table_name") or G::fatalError("PageUtils::getBlocks() : ".DATABASE_ERROR_MESSAGE);
        $items = array();
        while($row = $query->fetch()){
            $items[$row["id"]] = $row;
        }

        return $items;
    }

    /** Get news
     * @param string $type - news|promo
     * @param int $length
     * @return array
     */
    public static function getNews($type = "", $length = 0){
        $table_name = TABLE_NEWS;

        if ($type != ""){
            $condition = "WHERE type = '$type' ";
        }

        $query_str = "SELECT * FROM $table_name ".
            $condition .
            "ORDER BY date DESC " .
            ($length > 0 ? " LIMIT $length" : "");
        $query = G::$db->query($query_str) or G::fatalError("PageUtils::getNews() : ".DATABASE_ERROR_MESSAGE);
        $items = array();
        while($news = $query->fetch(PDO::FETCH_ASSOC)){
            $news["label"] = self::translit($news["short"], 128);
            $items[] = $news;
        }

        return $items;
    }

    /** Social networks data
     * @return array
     */
    public static function getSocials(){
        $result = array();
        $table_name = TABLE_SOCIALS;
        $query = G::$db->query("SELECT * FROM $table_name") or G::fatalError("PageUtils::getSocials() : ".DATABASE_ERROR_MESSAGE);
        while($row = $query->fetch()){
            $result[$row["name"]] = $row;
        }

        return $result;
    }

    /**
     * get all column names from table
     * @param $table_name
     */
    public static function getTableKeys($table_name){
        $query = G::$db->query("SHOW COLUMNS FROM $table_name");
        $keys = array();
        //G::logMessage("getTableKeys:: tablename = $table_name");
        while($row = $query->fetch()){
            $keys[] = $row[0];
        }
        return $keys;
    }

    /**
     * @param $value - value
     * @param $as - key name
     * @return array ("result" => true|false, "value" => valid_value|error_text)
     */
    public static function validatePageParam($value, $as){
        switch($as){
            case "id":
            case "currency_id":
                $value = intval($value);
                if($value > 0){
                   // ok, not isNaN
                }else{
                   $error = "$as is invalid";
                }
                break;
            case "type": // catalogue product
                $value = intval($value);
                if($value <= 0 || $value > 3){
                    $error = "$as is invalid";
                }
                break;
            case "parent_id":
            case "amount": // catalogue product
                $value = intval(abs($value)) | 0;
                break;
            case "access":
                $value = intval($value);
                if($value <= 0 || $value > 5){
                   $error = "ACCESS is invalid";
                }
                break;
            case "blocks_id":
                if (!preg_match("/(\d+\,*)+/", $value)){
                    $error = "$as is invalid";
                }
                break;
            case "meta_id":
                $value = intval($value);
                $query = G::$db->query("SELECT id FROM " . TABLE_META . " WHERE id='$value' LIMIT 1");
                $result = $query->fetchAll();
                if (!count($result)){
                    $error = "META_ID does not exists";
                }
                break;
            case "img_filename": // catalogue product
                if ($value
                    &&
                    !preg_match("/^(tmp|new)[\d]+(.jpg)$/", $value)
                ){
                    $error = "IMG_FILENAME is not valid";
                }
                break;
            case "label":
            //case "image": // catalogue product
                if (!preg_match(LABEL_REG_EXP, $value)){
                    $error = "$as is not valid";
                }
                break;
            case "content":

                # TODO validate CONTENT
                $value = mysql_real_escape_string( $value );

                break;

            case "props": // catalogue product
                if (!preg_match("/([a-z_0-9]=[a-z_0-9]\,?)*/", $value)){
                    $error = "PROPS is not valid";
                }
                break;
            case "children_id":
                if (!preg_match("/^(([0-9]+)(\,?))+$'/", $value)){
                    $error = "CHILDREN_ID is not valid";
                }
                break;

            // BOOLEAN
            case "hidden":
                if ($value === true || $value == "true" || $value == "1"){
                    $value = 1;
                }else{
                    $value = 0;
                }
                break;
            case "original_marking": // catalogue product
            // META DATA
            default:
            case "title":
            case "description":
            case "keywords":
            case "author":
            case "copyright":
            case "author_url":
            case "publisher_url":
            case "og_title":
            case "og_description":
            case "og_site_name":
                $value = G::$db->quote( strip_tags($value) );
                $value = preg_replace("/^\'|\'$/", "", $value); // remove side qoutes
                break;
            case "robots":
                if (!preg_match("/(index[,]*|follow[,]*|noindex[,]*|nofollow[,]*)+/", $value)){
                    $error = "ROBOTS is not valid";
                }
                break;
            case "og_url":
                if (!preg_match("/(^https?:\/\/)[\w\d\.\/\-\_\%]+$/", $value)){
                    $error = "OG:URL invalid URL format";
                }
                break;
            case "og_image":
                if (!preg_match("/(^https?:\/\/)[\w\d\.\/\-\_\%]+(.gif|.png|.jpg.jpeg)$/", $value)){
                    $error = "OG:IMAGE invalid IMAGE-URL format";
                }
                break;
            case "og_type":
                if (!preg_match("/[A-z:_.]+/", $value)){
                    $error = "OG:TYPE is not valid";
                }
                break;
            case "og_locale":
                if (!preg_match("/[a-z]{2}_[A-Z]{2}/", $value)){
                    $error = "OG:LOCALE is not valid";
                }
                break;
        }
        if (isset($error)){
            $result = false;
            $value = $error;
        }else{
            $result = true;
        }

        return array(
            "result" => $result,
            "value" => $value
        );

    }

    /**
     * @param string $type -> pages|articles|catalogue
     * @return array
     */
    public static function getItemsList($type="pages", $desc = false){
        $items = array();
        switch($type){
            default:
            case "pages":
                $t_data = TABLE_PAGES;
                $fields_str = "$t_data.id as id,label,title";
                break;
            case "articles":
                $t_data = TABLE_ARTICLES;
                $fields_str = "$t_data.id as id,label,title";
                break;
            case "catalogue":
                $t_data = TABLE_CATALOGUE;
                $fields_str = "$t_data.id as id,type,brand_id,original_marking,image,label,title,price,amount,hidden";
                break;
        }

        $t_meta = TABLE_META;

        $order = ($desc ? "ORDER BY id DESC" : "");

        $query = G::$db->query("SELECT $fields_str FROM $t_data LEFT JOIN $t_meta ON $t_data.meta_id = $t_meta.id $order") or G::fatalError("PageUtils::getPagesList() : " . print_r(G::$db->errorInfo(), true));
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $items[] = $row;
        }

        return $items;
    }

    /**
     * @param $page
     * @return array
     */
    public static function getPageNode($label){
        $t_pages = TABLE_PAGES;
        $t_meta = TABLE_META;

        $query_str = "SELECT *,".
                     "$t_pages.id as id ".
                     "FROM $t_pages ".
                     "LEFT JOIN $t_meta ON $t_pages.meta_id = $t_meta.id ".
                     "WHERE label='$label' ".
                     "LIMIT 1";
        $query = G::$db->query($query_str) or G::fatalError("PageUtils::getPageNode() : ".DATABASE_ERROR_MESSAGE);

        $node = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $node;
    }

    /**
     * @param $id - page id
     * @param $data (validated) - ( key => value )
     * @return bool
     */
    public static function updatePageNode($id, $data){
        $t_pages = TABLE_PAGES;
        $t_meta = TABLE_META;
        unset($data["id"]);
        $data_serialized = G::serializeUpdate($data);

        $query_str = "UPDATE $t_pages ".
            "LEFT JOIN $t_meta ON $t_pages.meta_id = $t_meta.id ".
            "SET " . $data_serialized . " " .
            "WHERE $t_pages.id='$id'";

        G::$db->exec($query_str);
        $result = intval(G::$db->errorCode());
        if ($result != 0){
            $error_info = G::$db->errorInfo();
            $text_error = " DB >> " . $error_info[1] . " > " . $error_info[2];
            G::fatalError("PageUtils::updatePageNode() >> " . $text_error);
        }

        return !$result;
    }

    /**
     * @param $data (validated) - array( key => value )
     * @return bool
     */
    public static function createPageNode($data){
        $t_pages = TABLE_PAGES;
        $t_meta = TABLE_META;
        $data_serialized = G::serializeInsert($data, $t_meta);

        $query_str = "INSERT INTO $t_meta " . $data_serialized;
        G::$db->exec($query_str) or G::fatalError("PageUtils::createPageNode() : META INSERT :".print_r(G::$db->errorInfo(), true));

        $data["meta_id"] = G::$db->lastInsertId();
        $data_serialized = G::serializeInsert($data, $t_pages);

        $query_str = "INSERT INTO $t_pages " . $data_serialized;
        G::$db->exec($query_str) or G::fatalError("PageUtils::createPageNode() : PAGES INSERT :".print_r(G::$db->errorInfo(), true));

        return true;
    }


    public static function deletePageNode(){
        // TODO delete page node
    }

    public static function parseItemUserData($created_str, $last_edited_str){
        if (!$created_str){
            $created_str = "0,0";
        }
        if (!$last_edited_str){
            $last_edited_str = "0,0";
        }
        $created_arr = explode(",", $created_str);
        $last_edited_arr = explode(",", $last_edited_str);
        $str = "Created by ";
        if (intval($created_arr[0]) > 0){
            $user_data = G::$user->getUserDataById($created_arr[0]);
            $str .= $user_data["name"] . "(id:" . $user_data["id"] . ") ";
        }else{
            $str .= "ADMIN ";
        }
        if (intval($created_arr[1]) > 0){
            $str .= date("d/m/y, H:i:s", $created_arr[1]);
        }
        $str .= "<br />Edited by ";
        if (intval($last_edited_arr[0]) > 0){
            $user_data =  G::$user->getUserDataById($last_edited_arr[0]);
            $str .= $user_data["name"] . "(id:" . $user_data["id"] . ") ";
        }else{
            $str .= "ADMIN ";
        }
        if (intval($last_edited_arr[1]) > 0){
            $str .= date("d/m/y, H:i:s", $last_edited_arr[1]);
        }
        return $str;
    }

    /**
     * @param $label
     * @return string - valid string whatever it was
     */
    public static function validatePageLabel($label){
        if (!isset($label)
            ||
            $label=="")
        {
            return PAGE_INDEX;
        }
        $label = strtolower($label);
        $label = trim($label);
        if (!preg_match(PAGE_LABEL_REG_EXP, $label))
        {
            // DEBUG
            G::fatalError("PageUtils::validatePageLabel() : Invalid Page");
            //

            return PAGE_PAGE404;
        }

        return $label;
    }

    /** Parses URL
     * @param $label
     * @return array - parsed address
     */
    public static function parsePageLabel($label){
        $result = array();
        $addr = preg_split("/\//", $label);
        // take off empty ones
        for($i = count($addr); $i >= 0; $i--){
            if (strlen($addr[$i]) == 0){
                unset($addr[$i]);
            }
        }
        // define type
        $result["type"] = array_shift($addr); // 0
        switch($result["type"]){
            default:
            # sample: /contacts
                $result["page"] = $result["type"];
                $result["type"] = (self::isTechnicalPage($result["page"]) ? P_TYPE_TECH : P_TYPE_PAGE);
                $result["address"] = $result["full_address"] = "/$result[page]";
                break;
            case P_TYPE_NEWS:
            # sample: /news/novost-translit
                $result["page"] = array_shift($addr); // 1
                $result["address"] = $result["full_address"] = "/$result[type]/$result[page]";
                break;
            case P_TYPE_BRAND:
            # sample: /news/novost-translit
                $result["page"] = array_shift($addr); // 1
                $result["address"] = $result["full_address"] = "/$result[type]/$result[page]";
                break;
            case P_TYPE_ADMIN:
            # sample: /admin/catalogue/add_product
                $result["page"] = array_shift($addr); // 1
                $result["action"] = array_shift($addr); // 2
                $result["address"] = "/$result[type]/$result[page]";
                $result["full_address"] = "/$result[type]/$result[page]/$result[action]";
                break;
            case P_TYPE_CATALOGUE:
            # sample: /catalogue/equipment/forest_black_perl
                array_unshift($addr, $result["type"]);
                $result["path"] = $addr;
                $result["address"] = $result["full_addres"] = "/".implode("/", $addr);
                break;
            case P_TYPE_ARTICLES:
            # sample: /articles/sdgprh_sdgw_wedddf
                $result["page"] = array_shift($addr); // 1
                $result["address"] = $result["full_address"] = "/$result[type]/$result[page]";
                break;
        }

        return $result;
    }

    /** Gets settings from DB
     * @return array - settings 'key' => 'value';
     */
    public static function getSettings(){
        $settings = array();
        $query = G::$db->query("SELECT * FROM ".TABLE_SETTINGS) or G::fatalError("PageUtils::getSettings() -> ".DATABASE_ERROR_MESSAGE);
        while($row = $query->fetch()){
            switch($row["name"]){
                default:
                    $value = $row["value"];
                    break;
                case "tel":
                    $value = explode(",", $row["value"]);
                    break;
            }
            $settings[$row["name"]] = $value;
        }

        return $settings;
    }
    /**
     * @return array - brands 'id' => array();
     */
    public static function getBrands(){
        $entities = array();

        $t_brands = TABLE_BRANDS;
        $t_countries = TABLE_COUNTRIES;

        $query_str = "SELECT *, " .
            "$t_brands.id AS id,".
            "$t_brands.name AS name,".
            "$t_brands.full_name AS full_name,".
            "$t_countries.id AS country_id,".
            "$t_countries.short_name AS country_short_name,".
            "$t_countries.full_name AS country_full_name ".
            "FROM $t_brands " .
            "LEFT JOIN $t_countries ON $t_brands.country_code = $t_countries.unicode";

        $query = G::$db->query($query_str) or G::fatalError("PageUtils::getBrands() -> ".DATABASE_ERROR_MESSAGE);
        while($row = $query->fetch()){
            $entities[$row["id"]] = new BrandEntity($row);
        }

        return $entities;
    }


    /**
     * @return array - menu [ 'menu_title'=>[ ['key'=>'value'], ['key'=>'value'], ... ] ]
     */
    public static function getAllMenu(){
        $menu = array();

        $t_menu = TABLE_MENU;
        $t_items = TABLE_MENU_ITEMS;
        $query_menu = G::$db->query("SELECT * FROM $t_menu");

        while($row_menu = $query_menu->fetch()){
            $title = $row_menu["title"];
            $items_id = $row_menu["items_id"];
            $menu[$title] = array();
            $items_id_arr = explode(",", $items_id);
            if(strlen($items_id) <= 0){
                continue;
            }

            $query_items = G::$db->query("SELECT * FROM $t_items WHERE id IN ($items_id)");
            while($row = $query_items->fetch(PDO::FETCH_ASSOC)){
                $pos = array_search($row["id"], $items_id_arr);
                $menu[$title][$pos] = $row;
            }
        }

        /*
        // DEBUG
        echo "<pre>";
        print_r($menu);
        echo "</pre>";
        */

        return $menu;
    }

    public static function getAllCurrency(){
        $entities = array();
        $query = G::$db->query("SELECT * FROM ".TABLE_CURRENCY) or G::fatalError("PageUtils::getAllCurrency() -> ".DATABASE_ERROR_MESSAGE);
        while($row = $query->fetch()){
            $entities[$row["id"]] = $row;
        }

        return $entities;
    }

    public static function labelExists($label, $table_name = TABLE_PAGES){
        if (!isset($label)){
            return false;
        }
        $query = G::$db->query("SELECT label FROM " . $table_name . " WHERE label='$label'");
        $result = $query->fetchAll();

        return count($result) > 0;
    }

    public static function getIdByLabel($label, $table_name = TABLE_PAGES){
        if (!$label){
            return 0;
        }
        $query = G::$db->query("SELECT id FROM " . $table_name . " WHERE label='$label' LIMIT 1");
        $result = $query->fetch();
        $query->closeCursor();

        return $result["id"];
    }

    public static function getLabelById($id, $table_name = TABLE_PAGES){
        $id = intval($id);
        if (!$id){
            return "";
        }
        $query = G::$db->query("SELECT label FROM " . $table_name . " WHERE id='$id' LIMIT 1");
        $result = $query->fetch();
        $query->closeCursor();

        return $result["label"];
    }

    /**
     * Transliteration of cyrillic into URL-string
     */
    public static function translit($text, $length = 60){
        $result = URLify::filter(iconv("windows-1251", "utf-8", $text), $length);
        return $result;
    }

}
?>