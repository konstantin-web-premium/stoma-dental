<?php
### THIS PAGE SEND WITH AJAX

$mModule = G::$pageData->getCurrentAdminModule();
$categories = $mModule->getProp("categoriesTree");


/*
echo "<pre>";
print_r($categories);
echo "</pre>";
die();
*/


function renderElement($data){
    if($data["type"] == TYPE_CATEGORY){
        $len = count($data["children"]) | 0;
        $arrow = "&nbsp;&#10148;&nbsp;";
        // title
        echo $data["title"] .
            "<sup>[" . $len . "]</sup> " .
            "<span class='small-text'>" .
            "[<a href=\"javascript:popupWindow('editAdminCategory', '$data[id]')\">edit</a>] " .
            "[<a href='delete-category#$data[label]'>delete</a>]" .
            "</span>";

        // children
        echo "<div class='spoilers-tree' spoiler='true' title='$arrow'>";
        if ($len){
            foreach($data["children"] as $child){
                renderElement($child);
            }
        }else{
            echo "<span class='grey-text'>empty</span>";
        }
        echo "</div>";
    }else{
        echo "<span class='grey-text'>$data[title]</span>";
    }

    echo "<br />\n";
    return;
}

if (!$categories || !count($categories)){
    echo "<span class='grey-small-text'>NO CATEGORIES</span>";
}else{
    foreach($categories["children"] as $category){
        renderElement($category);
    }
}
?>