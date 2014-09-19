<?php
//
$categories = G::$pageData->data["current_categories"];
$root = "/" . CATALOGUE_ROOT_LABEL . "/";
$catalog_text = G::$language->getText("common", "catalogue_link_text");
?>

<ul class="navigation">
    <?php

    for($i = 0; $i < count($categories); $i++){
        $product = $categories[$i];
        if ($product->hidden){
            continue;
        }


        ?>
        <li>
            <img src="<?php echo $product->getImageUrl("small");?>" />
            <a href="<?php echo $root . $product->label . "/";?>"><?php echo $product->title;?></a>
        </li>
        <?php

    }

    ?>
</ul>
<p>&nbsp;</p>