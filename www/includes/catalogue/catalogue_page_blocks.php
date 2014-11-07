<?php
// Products\category list View

$products = G::$pageData->data["products"];
$text_buy = G::$language->getText("common", "buy");
$text_order = G::$language->getText("common", "order");
$text_request_price = G::$language->getText("common", "request_price");
$content = G::$pageData->data["content"];

if (G::$pageData->data["type"] == TYPE_CATEGORY && $content != ""){

    ?>
    <div class="catalogue-top-content">
        <?php echo $content;?>
    </div>
    <?php

}

?>
<ul class="block-list">
<?php
if ($products){
    for($i = 0; $i < count($products); $i++){
        $product = $products[$i];
        if ($product->hidden){
            continue;
        }
        $image_url = $product->getImageUrl("medium");
        $title = $product->title;
        $content = $product->content;
        $description = $product->description;
        $price_uah = intval($product->getPrice("UAH"));
        $price_uah_formatted = $product->getPrice("UAH", true);
        $price_usd = intval($product->getPrice("USD"));
        $price_usd_formatted = $product->getPrice("USD", true);
        $props = $product->getProps();

        //SPIKE
        $spike_var = (G::$pageData->getAddress("page") == PAGE_INDEX ? "catalogue/" : "");
        $href = $spike_var . $product->label;
        //

        $brand_logo = "";
        $brand = G::$pageData->getBrand($product->brand_id);
        if ($brand){
            $brand_logo = $brand->getLogoUrl("small");
        }

        switch((string)$product->type){
            //CATEGORY------------------------------------------------------------------
            case TYPE_CATEGORY:

                ?>
                <li class="category-item">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>" />
                    <a href="<?php echo $href . "/";?>"><?php echo $title; ?></a>
                    <p class="description">
                        <?php echo $description; ?>
                    </p>
                </li>
                <?php

                break;
            // PRODUCT  BIG--------------------------------------------------------------
            case TYPE_PRODUCT_BIG:
            // PRODUCT  SMALL-------------------------------------------------------------
            case TYPE_PRODUCT_SMALL:

                ?>
                <li class="product-item">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>" />
                    <?php

                    if ($price_uah > 0){

                        ?>
                        <div class="price-block">
                            <span class="price-native"><?php echo $price_uah_formatted;?>&nbsp;UAH</span>
                            <br />
                            <span class="price-foreign"><?php echo $price_usd_formatted;?>&nbsp;$</span>
                        </div>
                        <?php

                    }

                    echo "<div class='product-options'>";

                    ?>
                    <div class="product-title">
                        <a href="<?php echo $href . "/";?>"><?php echo $title; ?></a>
                    </div>
                    <?php

                    if ($brand_logo){
                        echo "<img class=\"brand-logo\" src=\"$brand_logo\" />";
                    }

                    // BRAND COUNTRY --------------------------------------------

                    if ($brand){
                        echo "<div class='country'>$brand->name (" . $brand->getCountry() . ")</div>";
                    }

                    // OPTIONS TABLE --------------------------------------------

                    if ($props){

                        echo "<table class=\"options-table\" align=\"center\">";

                        foreach($props as $prop){
                            if (!$prop->hidden && !$prop->after_choose){

                                ?>
                                <tr>
                                    <td><?php echo $prop->title;?>:</td>
                                    <td width = "30%" align="right"><?php echo $prop->render();?></td>
                                </tr>
                                <?php

                            }
                        }
                        echo "</table>\n";
                    }else{
//                        echo "<div class='description'>$description</div>";
                    }
                    echo "<div class='description'>$description</div>";

                    echo "</div>";

                    /*
                     * HIDE
                    // BUY LINK (add_to_cart) -------------------------------------------------
                    if ($price_uah > 0){
                        if ($product->amount > 0){
                            $buy_link = "<a href=\"#\" class=\"product-buy\">$text_buy!</a>";
                        }else{
                            $buy_link = "<a href=\"#\" class=\"product-buy\">$text_order!</a>";
                        }
                    }else{
                        $buy_link = "<a href=\"#\" class=\"product-request-price\">$text_request_price</a>";
                    }

                    echo $buy_link;
                    */

                    ?>
                </li>
                <?php

                break;
            } // switch
    }// foreach
}
?>
</ul>