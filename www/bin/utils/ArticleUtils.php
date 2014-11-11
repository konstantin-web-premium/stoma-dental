<?php
include_once ($_SERVER["DOCUMENT_ROOT"] . "/external/urlify-master/URLify.php");

class ArticleUtils{

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
     * @param $page
     * @return array
     */
    public static function getArticleNode($label){
        $t_nodes = TABLE_ARTICLES;
        $t_users = TABLE_USERS;
        $t_meta = TABLE_META;

        $query_str = "SELECT *,".
                     "$t_nodes.id as id ".
                     "FROM $t_nodes ".
                     "LEFT JOIN $t_meta ON $t_nodes.meta_id = $t_meta.id ".
                     "LEFT JOIN $t_users ON $t_nodes.author_id = $t_users.id ".
                     "WHERE label='$label' ".
                     "LIMIT 1";
        $query = G::$db->query($query_str) or G::fatalError("ArticleUtils::getArticleNode() : ".DATABASE_ERROR_MESSAGE);

        $node = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $node;
    }

    public static function getArticles($limit){
        $t_nodes = TABLE_ARTICLES;
        $t_users = TABLE_USERS;

        $query_str = "SELECT *," .
            "$t_nodes.id as id ".
            "FROM $t_nodes ".
            "LEFT JOIN $t_users ON $t_nodes.author_id = $t_users.id ".
            "ORDER BY created_time DESC " .
            ($limit > 0 ? "LIMIT $limit" : "");
        $query = G::$db->query($query_str) or G::fatalError("ArticleUtils::getArticles() : ".DATABASE_ERROR_MESSAGE);

        $list = $query->fetchAll(PDO::FETCH_ASSOC);
        return $list;
    }

}
?>