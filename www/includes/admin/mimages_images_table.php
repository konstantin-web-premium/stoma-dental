<?php
$products = PageUtils::getItemsList("catalogue", true);
$action = G::$pageData->imagesType;
switch ($action){
    default:
    case "pages":
        $path = "/images/pages/";
        $table_title = "Pages";
        $view_path = "/";
        break;
    case "c_small":
        $table_title = "Catalog - small (40 x 40) miniature";
        $path = "/images/catalogue/small/";
        $image_type = "small";
        $view_path = "/catalogue/";
        break;
    case "c_medium":
        $table_title = "Catalog - medium (270 x 180) miniature";
        $path = "/images/catalogue/medium/";
        $image_type = "medium";
        $view_path = "/catalogue/";
        break;
    case "c_large":
        $table_title = "Catalog - large image";
        $path = "/images/catalogue/large/";
        $image_type = "large";
        $view_path = "/catalogue/";
        break;
    case "news":
        $table_title = "News images";
        $path = "/images/news/";
        $view_path = "/news/";
        break;
    case "articles":
        $table_title = "Articles images";
        $path = "/images/articles/";
        $view_path = "/articles/";
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

<h3><?php echo $table_title;?></h3>
<button class="button-css-blue" id="table_clean_filters">Clear filters</button>
<span id="table_rowcount"></span>
<div style="float: right;">
    <button class="button-css-blue" id="button_minimize_table">_</button>
</div>
<div class="semi-spoiler" height="200">
    <table id="table_images_list" class="nodes_list_table">
        <thead>
            <tr>
                <th width="30px">#</th>
                <th width="50px">image</th>
                <th width="250px">file</th>
                <th width="60px" filter-type='ddl'>type</th>
                <th>label</th>
                <th width="250px">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
    <?php
    for($i = 0; $i < count($files); $i++){
        $file = $files[$i];
        $file_name = substr($file, 0, stripos($file, "."));
        $labels = array();
        foreach($products as $pr){
            if (preg_match("/[\=?\&?]/", $pr["image"])){
                parse_str($pr["image"], $image_data);
                $image = $image_data[$image_type];
                //DEBUG
                G::logMessage("image >>> " . $image . " " . print_r($image_data, true));
                //
            }else{
                // DEBUG
                $image = $pr["image"];
                //
            }
            if ($image == $file_name){
                $labels[] = "(<a href='" . $view_path . $pr["label"] . "' target='_blank'>$pr[label]</a>) $pr[title]";
            }
        }
        $text = implode("<br />", $labels);
        echo RenderUtils::renderImageDataTableRow($i, $path, $file, $text);
    }
    ?>
        </tbody>
    </table>
</div>