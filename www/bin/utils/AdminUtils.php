<?php
include_once "RenderUtils.php";

class AdminUtils{
    public static $db_link;
// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

    private static function cropImage($img, $width, $height, $x, $y, $new_width = 270, $new_height = 180){
        $img_width = imagesx($img);
        $img_height = imagesy($img);

        G::logMessage(">> >> start with ($new_width x $new_height)");
        $thumb = imagecreatetruecolor( $new_width, $new_height );
        G::logMessage(">> >> thumb start = " . $thumb);

        // Resize and crop
        imagecopyresampled(
            $thumb,                 // resource $dst_image
            $img,                   // resource $src_image
            0, 0,                   // int $dst_x , int $dst_y
            $x, $y,                 // int $src_x , int $src_y
            $new_width, $new_height,// int $dst_w , int $dst_h
            $width, $height         // int $src_w , int $src_h
        );

        G::logMessage(">> >> thumb cropped = " . $thumb);

        return $thumb;
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public static function addMenu($title){

    }

    public static function getMenuList(){
        $list = array();
        $query = G::$db->query("SELECT * FROM ".TABLE_MENU);
        while($row = $query->fetch()){
            $list[$row["id"]] = $row["title"];
        }
        return $list;
    }

    public static function getMenuItemsList(){
        $list = array();
        $query = G::$db->query("SELECT * FROM ".TABLE_MENU_ITEMS);
        while($row = $query->fetch()){
            $list[$row["id"]] = $row["content"];
        }
        return $list;
    }

    public static function addCategory($label, $title, $children_id = ''){
        $type = 1;
        $t_cat = TABLE_CATALOGUE;
        $query = mysql_query("INSERT INTO $t_cat (type, label, title, children_id) VALUES('$type', '$label', '$title', '$children_id')", self::$db_link);
        return mysql_insert_id();
    }

    public static function addChild($parent, $id){
        $t_cat = TABLE_CATALOGUE;
        $query = mysql_query("UPDATE $t_cat SET children_id = CONCAT_WS(',', children_id, $id) WHERE id='$parent'", self::$db_link);
        return mysql_insert_id();
    }

    /**
     * @param $label - validated label!
     * @param int $id - if need to check label's existance
     * @return bool
     */
    public static function categoryLabelExists($label, $id = 0){
        $t_cat = TABLE_CATALOGUE;
        $check_id = ($id == 0 ? "" : "id != $id AND ");
        $query = mysql_query("SELECT label FROM $t_cat WHERE $check_id label=$label LIMIT 1", self::$db_link);
        if ($query){
            return true;
        }
        return false;
    }

    public static function editCatalogueCategory($id, $newLabel, $newTitle = "", $newChildren_id = ""){
        $t_cat = TABLE_CATALOGUE;

        $values = array();
        $values[] = "label = '$newLabel'";
        if (strlen($newTitle) > 0){
            $values[] = "title = '$newTitle'";
        }
        if (strlen($newChildren_id) > 0){
            $values[] = "children_id = '$newChildren_id'";
        }
        $set_values = implode(",", $values);


        $query = mysql_query("UPDATE $t_cat SET $set_values WHERE id = $id LIMIT 1", self::$db_link);
        if(!$query){
            return false;
        }
        $row = mysql_fetch_assoc($query);
    }


    public static function uploadProductImage($image_data){
        $data = array();
        $data["errors"] = array();
        $data["warnings"] = array();
        $data["result"] = false;
        if ($image_data["type"] != "image/jpeg"){
            $data["errors"][] = "Wrong image type (" . $image_data["type"] . ")";
            G::logMessage("AdminUtils::uploadProductImage() > Wrong image type (" . $image_data["type"] . ")");
        }
        if ($image_data["size"] > 5 * 1024 * 1024){
            $data["errors"][] = "Too large image (" . $image_data["size"] . ")";
            G::logMessage("AdminUtils::uploadProductImage() > Too large image (" . $image_data["size"] . ")");
        }
        if ($image_data["error"] > 0){
            $data["errors"][] = "Error occurred - code:" . $_FILES["error"];
            G::logMessage("AdminUtils::uploadProductImage() > Error occurred - code:" . $image_data["error"]);
        }
        if (count($data["errors"]) == 0){
            $img = @imagecreatefromjpeg($image_data["tmp_name"]);
            list($width, $height) = @getimagesize($image_data["tmp_name"]);
            unlink($image_data["tmp_name"]);
            if (!$img){
                $data["errors"][] = "Invalid image";
                G::logMessage("AdminUtils::uploadProductImage() > Invalid image");
            }else{
                if ($width < 270){
                    $data["warnings"][] = "Image width is less than 270 pixels";
                }
                if ($height < 180){
                    $data["warnings"][] = "Image height is less than 180 pixels";
                }
                $img_filename = "tmp" . date("U") . (rand(0,10000) * 10000) . ".jpg";
                imagejpeg($img, (ROOT . PATH_CAT_IMAGES . "_tmp/" . $img_filename) );
                imagedestroy($img);
                $data["img_filename"] = $img_filename;
                $data["img_width"] = $width;
                $data["img_height"] = $height;
                $data["result"] = true;
            }
        }
        return $data;
    }


    public static function cropProductImage($filename, $width, $height, $x = 0, $y = 0, $new_width = 0, $new_height = 0){
        $data = array();
        $file_path = ROOT . PATH_CAT_IMAGES . "_tmp/";
        $data["img_filename"] = $new_filename = "new" . date("U") . rand(100,10000000) . ".jpg";
        if (file_exists($file_path . $filename)){
            $img = @imagecreatefromjpeg($file_path . $filename);                            // get image
            G::logMessage(">> img = $img");
            if ($new_width == 0 || $new_height == 0){
                $new_width = $width;
                $new_height = $height;
            }
            G::logMessage(">> size = $width x $height");
            $img = self::cropImage($img, $width, $height, $x, $y, $new_width, $new_height); // crop and resize
            G::logMessage(">> $img cropped = $img");
            $data["result"] = imagejpeg($img, ($file_path . $new_filename) );               // save image
            G::logMessage(">> result = " . $data["result"]);
            imagedestroy($img);                                                             // unload reference
            unlink($file_path . $filename);                                                 // delete tmp file
        }else{
            G::logMessage("NO file");
        }
        return $data;
    }

    public static function createInitialPages(){
        // TODO create
        // index, 404, register, access_denied,

        // contacts, articles, help, service, delivery, about, education
    }

}
?>