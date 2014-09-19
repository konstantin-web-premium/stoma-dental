<?php
// Product (single) View

$product = G::$pageData->data["current_product"];
$text_buy = G::$language->getText("common", "buy");
$text_order = G::$language->getText("common", "order");
$text_request_price = G::$language->getText("common", "request_price");
$text_manuals = G::$language->getText("common", "manuals_text");
$text_articles = G::$language->getText("common", "articles_text");
$text_description = G::$language->getText("common", "description_text");

if ($product){
    $image_url = $product->getImageUrl("large");
    $title = $product->title;
    // TODO price convertor
    $price_uah   = $product->getPrice("UAH");
    $price_usd   = $product->getPrice("USD");
    $description = $product->description;
    $content     = $product->content;
    $amount      = $product->amount;
    $props       = $product->getProps();

    $href = $product->label;

    $brand = G::$pageData->getBrand($product->brand_id);
}
?>
<div class="catalogue-single-block">
    <img src="<?php echo $image_url; ?>" />
    <h2><?php echo $product->title;?></h2>
    <?php

    // BRAND
    if($brand){

        echo "<span style='font-size:smaller;'>" . $brand->name . " (" . $brand->getCountry() . ")</span>\n";

    }

    // OPTIONS
    if($props){
        // TODO, DEBUG SPIKE ------
        if (G::$user->isOrHigher(U_ADMIN)){
            $inited = 0;
            foreach($props as $prop){
                if ($prop->after_choose){
                    if ($inited == 0){
                        echo "<div class='options-block'>\n";
                        $inited = 1;
                    }
                    echo "<div class='input-block'>" . $prop->title . "<br />" . $prop->render() . "</div>";
                }

            }
            if ($inited == 1){
                echo "</div>";
            }
        }

        // READ ONLY PROPS (non select)

        $inited = 0;
        foreach($props as $prop){
            if (!$prop->after_choose){
                if ($inited == 0){
                    echo "<div class='options-block'>\n";
                    $inited = 1;
                }
                echo $prop->title . ": " . $prop->render() . "<br />";
            }

        }
        if ($inited == 1){
            echo "</div>";
        }
    }
    // -----

    // PRICE
    /* HIDE TEMPORARY
    if ($price_uah > 0){
        echo "<div class='price-block'>\n".
            ($amount > 0
                ? "<a href='#' class='product-buy'>$text_buy</a>\n"
                : "<a href='#' class='product-order'>$text_order</a>\n").
            "<span class='price-native'>$price_uah UAH</span><br />".
            "<span class='price-foreign'>$price_usd $</span>".
            "</div>";
    }else{
        echo "<a href='#' class='product-request-price'>$text_request_price</a>\n";
    }
    */
    echo "<br />&nbsp;<br />\n";

    // TABS ---------------

    ?>
    <div class="tab-section">
        <ul class="tabs">
            <?php
            echo "<li class='current'>$text_description</li>";
            //echo (!$articles ? "<li>$text_articles</li>" :"");
            //echo (!$manuals ? "<li>$text_manuals</li>" :"");
            ?>
        </ul>
        <?php

        echo "<div class='tab-box visible'>\n" .
            $content .
            "</div>";

// TODO ARTICLES AND MANUALS
/*
        if (!$articles){
            echo "<div class='tab-box'>\n" .
                $text_articles .
                "</div>\n";
        }

        if (!$manuals){
            echo "<div class='tab-box'>\n" .
                $text_manuals .
                "</div>\n";
        }

*/
        ?>
    </div><!-- .section -->
</div>