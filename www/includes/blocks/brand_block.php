<?php
$text_country = G::$language->getText("common", "country_text");
$text_website = G::$language->getText("common", "website_text");
$text_producer = G::$language->getText("common", "producer_text");

$products = G::$pageData->data["products"]; // count == 1;
$product = $products[0];
$brand = G::$pageData->getBrand($product->brand_id);
if ($brand){
    $img_url = $brand->getLogoUrl("medium");

?>
<h1><?php echo $text_producer; ?></h1>
<div class="brand-side-block">
    <img src="<?php echo $img_url;?>" />
    <strong><?php echo $brand->name;?></strong>
    <p>
        <?php echo $text_country . ": " . $brand->country; ?>
        <br />
        <?php

        //echo "$text_website: <a rel=\"nofollow\" target=\"_blank\" href=\"$brand->url\">$brand->url</a><br/>";

        ?>
        <br />
        <?php echo $brand->description; ?>
    </p>
</div>
<?php

}

?>