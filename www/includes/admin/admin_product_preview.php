<?php
// Products\category list View
$text_buy = G::$language->getText("common", "buy");
$text_order = G::$language->getText("common", "order");
$text_request_price = G::$language->getText("common", "request_price");

?>

<div class="small-preview">
    <div class="product-preview-image-buttons">
        Preview SMALL 40x40
        <br />
        <button type="button" id="button_delete_image" class="button-css-blue">Delete image</button>
        <button type="button" id="button_change_image_small" class="button-css-blue">Change image</button>
    </div>

    <ul class="navigation">
        <li>
            <img src="" name="image_preview" alt="" />
            <a href="#" name="title">Title</a>
        </li>
    </ul>
</div>

<div class="product-preview-image-buttons">
    Preview MEDIUM
    <br />
    <button type="button" id="button_delete_image" class="button-css-blue">Delete image</button>
    <button type="button" id="button_change_image_medium" class="button-css-blue">Change image</button>
</div>

<ul class="block-list">
    <div class="category-item">

        <!-- IMAGE 100 x 100 -->
        <img name="image_preview" src="" alt="" />
        !-- // -->

        <a name="title" href="#">Title</a>
        <p class='description'>
            $description;
        </p>
    </div>
<br />
</ul>

<ul class="block-list">
    <li class="product-item">

        <!-- IMAGE 270 x 180 -->
        <img name='image_preview' src="/images/catalogue/medium/nophoto.jpg"/>
        <!-- // -->

        <div class="price-block">
            <span class="price-native">0&nbsp;UAH</span>
            <br />
            <span class="price-foreign">0&nbsp;$</span>
        </div>
        <div class='product-options'>
            <div class="product-title">
                <a name="title" href="#">Title</a>
            </div>
            <img class="brand-logo" src="" alt="" />
            <div class="country">Name (Country)</div>
            <table class="options-table" align="center">
                <tr>
                    <td>$prop->title</td>
                    <td width = "30%" align="right">$prop->render()</td>
                </tr>
            </table>
            <div class='description'>$description</div>
        </div>
        <a href="#" class="product-buy"><?php echo $text_buy;?>!</a>
        <a href="#" class="product-order"><?php echo $text_order;?>!</a>
        <a href="#" class="product-request-price"><?php echo $text_request_price;?></a>
    </li>
</ul>

<p>&nbsp;</p>

<div class="large-preview">
    <div class="product-preview-image-buttons">
        Preview LARGE
        <br />
        <button type="button" id="button_delete_image_large" class="button-css-blue">Delete image</button>
        <button type="button" id="button_change_image_large" class="button-css-blue">Change image</button>
    </div>
    <!-- IMAGE ~500 x ~500 -->
    <img name='image_preview' src="/images/catalogue/large/nophoto.jpg" />
    <!-- // -->
</div>

<div class="popup-window" id="popup_image_changer">
    <div class="popup-window-content">
        <table>

        </table>
    </div>
</div>

