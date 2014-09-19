<?php
/*
current catalogue path

$items_arr[$i] => array( id, label, title, children_id )
*/
?>
<div class="catalog-address-line">
    <?php
    $items_arr = G::$pageData->data["catalogue"]["path_items"];

    $url = "/";
    for($i = 0; $i < count($items_arr); $i++){
        $title = $items_arr[$i]["title"];
        $url .= $items_arr[$i]["label"]."/";
        if ($i == count($items_arr)-1){
            echo "<span class='last-item'>$title</span>";
        }else{
            echo "<a href='$url'>$title</a><span class='arrow-pointer'>&#10148;</span>";
        }
    }
    ?>
</div>