<?php
class RenderUtils{

    public function __construct(){

    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public static function renderCSSLink($filename){
        return "<link rel=\"stylesheet\" href=\"" . PATH_STYLES . $filename . "\" type=\"text/css\" />";
    }

    public static function renderScriptLink($link, $external = false, $type = "text/javascript"){
        //http://code.jquery.com/jquery-2.0.2.min.js
        $src = ($external ? "" : PATH_SCRIPTS) . $link;
        return "<script src=\"$src\" type=\"$type\"></script>";
    }

    public static function renderError($error_text){
        return "<span class=\"error-block\">Error: ".$error_text."</span>";
    }

    public static function renderNodeTableRow($node){

        if (is_object($node)){
            $node = (array) $node;
        }
        $str = "";
        $type_bkg = "";
        $row_bkg = "";
        $brand_bkg = "";
        $image_bkg = "";
        $label_bkg = "";
        $price_bkg = "";
        $amount_bkg = "";

        switch($node["type"]){
            default:
                $type = "unknown";
                $row_bkg = "#fcc";
                break;
            case TYPE_CATEGORY:
                $type = "category";
                $row_bkg = "#efe";
                break;
            case TYPE_PRODUCT_BIG:
                $type = "big";
                $row_bkg = "#def";
                break;
            case TYPE_PRODUCT_SMALL:
                $type = "small";
                break;
        }

        if ($node["hidden"] == "1"){
            $type = $type . " (hidden)";
            $row_bkg = "#999";
        }

        $brand = G::$pageData->getBrand($node["brand_id"]);
        if ($brand){
            $brand_name = $brand->name;
        }else{
            if ($node["type"] == TYPE_CATEGORY){
                $brand_name = "";
            }else{
                $brand_name = "unknown (id=$node[brand_id])";
                $brand_bkg = "#fcc";
            }
        }

        if($node["image"] == ""){
            $image_bkg = "#fcc";
        }

        $valid = PageUtils::validatePageParam($node["label"], "label");
        if (!$valid["result"]){
            $label_bkg = "#fcc";
        }

        $price = $node["price"];
        $amount = $node["amount"];
        if ($node["type"] == TYPE_CATEGORY){
            $price = "";
            $amount = "";
        }else{
            if ($node["price"] <= 0){
                $price_bkg = "#fe3";
            }
            if ($node["amount"] <= 0){
                $amount_bkg = "#fe3";
            }
        }

        $str .= "<tr " . ($row_bkg ? "bgcolor='$row_bkg'" : "") . " name=$node[id]>" .
            "<td>$node[id]</td>" .
            "<td " . ($type_bkg ? "bgcolor='$type_bkg'" : "") . ">$type</td>" .
            "<td " . ($brand_bkg ? "bgcolor='$brand_bkg'" : "") . ">$brand_name</td>" .
            "<td>$node[original_marking]</td>" .
            "<td " . ($image_bkg ? "bgcolor='$image_bkg'" : "") . ">$node[image]</td>" .
            "<td " . ($label_bkg ? "bgcolor='$label_bkg'" : "") . ">$node[label]</td>" .
            "<td>$node[title]</td>" .
            "<td " . ($price_bkg ? "bgcolor='$price_bkg'" : "") . ">$price</td>" .
            "<td " . ($amount_bkg ? "bgcolor='$amount_bkg'" : "") . ">$amount</td>" .
            "</tr>\n";

        /*
        $str .= "<tr " . ($row_bkg ? "bgcolor=$row_bkg" : "") . " name=$node[id]>" .
            "<td>$node[id]</td>" .
            "<td " . ($type_bkg ? "bgcolor=$type_bkg" : "") . ">$type</td>" .
            "<td " . ($brand_bkg ? "bgcolor=$brand_bkg" : "") . ">$brand_name</td>" .
            "<td>$node[original_marking]</td>" .
            "<td " . ($image_bkg ? "bgcolor=$image_bkg" : "") . ">$node[image]</td>" .
            "<td " . ($label_bkg ? "bgcolor=$label_bkg" : "") . ">$node[label]</td>" .
            "<td>$node[title]</td>" .
            "<td " . ($price_bkg ? "bgcolor=$price_bkg" : "") . ">$price</td>" .
            "<td " . ($amount_bkg ? "bgcolor=$amount_bkg" : "") . ">$amount</td>" .
            "</tr>\n";    }
        */
        return self::encodingChecked($str);
    }


    public static function renderImageDataTableRow($n = 1, $path="", $file="", $text=""){
        $file_type = substr($file, stripos($file, ".")+1);
        $file_name = substr($file, 0, stripos($file, "."));
        $button_str = "<button class='button-css-red' type='button' name='delete_image'>X</button>";
        $message = "Can't delete, remove image from product first";
        if ($file_name == "nophoto"){
            $message = "Can't delete this image!";
            $text = "<span class='grey-text'>This is technical image.</span>";
        }
        list($img_width, $img_height) = @getimagesize(ROOT . $path . "/" . $file);

        $str = "<tr>" .
            "<td>$i</td>" .
            "<td><a href='$path/$file' target='_blank'><img src='$path/$file' alt='' class='admin_preview_mini' /></a></td>" .
            "<td name='$file_name'>$file_name<br />($img_width x $img_height)</td>" .
            "<td>$file_type</td>" .
            "<td>" . $text . "</td>" .
            "<td>" . (strlen($text) ? "<span class='grey-small-text'>$message</span>" : $button_str) . "</td>" .
            "</tr>\n";
        return self::encodingChecked($str);
    }

    public static function encodingChecked($str){
        if (G::$dataType == G::DATATYPE_AJAX && G::DATATYPE_AJAX_HTML){
            $str = iconv("windows-1251", "utf-8", $str);
        }
        return $str;
    }
}
?>