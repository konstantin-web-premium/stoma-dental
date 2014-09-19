<?php
$products = PageUtils::getItemsList("catalogue", true);
$action = G::$pageData->imagesType;
switch ($action){
    default:
    case "pages":
        $path = "/images/pages/";
        $table_title = "Pages";
        break;
    case "c_small":
        $table_title = "Catalog - small (40 x 40) miniature";
        $path = "/images/catalogue/small/";
        $image_type = "small";
        break;
    case "c_medium":
        $table_title = "Catalog - medium (270 x 180) miniature";
        $path = "/images/catalogue/medium/";
        $image_type = "medium";
        break;
    case "c_large":
        $table_title = "Catalog - large image";
        $path = "/images/catalogue/large/";
        $image_type = "large";
        break;
    case "news":
        $table_title = "News images";
        $path = "/images/news/";
        break;
    case "articles":
        $table_title = "Articles images";
        $path = "/images/articles/";
        break;
}
$dir = opendir (ROOT . $path);
$files = array();
while ($file = readdir($dir)){
    if(($file != ".") && ($file != "..")){
        $files[] = $file;
    }
}
closedir($dir);
?>
<button name="cancel" class="button-css-blue" style="float:right;">X</button>
<div class="content">

    <h3><?php echo $table_title;?></h3>

    <div class="images_list_blocks">
        <?php
        for($i = 0; $i < count($files); $i++){
            $file = $files[$i];
            $file_name = substr($file, 0, stripos($file, "."));
            $labels = array();
            foreach($products as $pr){
                if (preg_match("/[\=?\&?]/", $pr["image"])){
                    parse_str($pr["image"], $image_data);
                    $image = $image_data[$image_type];
                }else{
                    // DEBUG
                    $image = $pr["image"];
                    //
                }
                if ($image == $file_name){
                    $labels[] = $pr[title];
                }
            }
            $text = implode("<br />", $labels);
            list($img_width, $img_height) = @getimagesize(ROOT . $path . "/" . $file);

            echo "<div class='popup-images-list-block'>" .
                "<a href=\"javascript:onImageSelected('$file_name')\"><img src='$path/$file' alt='' /></a>" .
                (count($labels) ? "&nbsp;$img_width x $img_height<br /><ul><li>" . implode("</li><li>", $labels) . "</li></ul>" : "") .
                "&nbsp;[<a href='$path/$file' target='_blank'>View >>></a>]" .
                "<br />" .
                "</div>";

        }
        ?>
    </div>
</div>