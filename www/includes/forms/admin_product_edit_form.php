<?php
$products = G::$pageData->getItemsList("catalogue");
$currency = G::$pageData->getCurrency();
$brands = G::$pageData->getBrands();
?>


<form id="product_edit_form" method="POST" accept-charset="utf-8">
    <div name="errors_field"></div>
    <div class="user-data-block"></div>
    <h3 name="form_title">Edit: </h3>


    <input type="hidden" name="id" value="0"/>
    <input type="hidden" name="edit_type" value="catalogue" />
    <input type="hidden" name="img_small" value="" />
    <input type="hidden" name="img_medium" value="" />
    <input type="hidden" name="img_large" value="" />

    <div>
        <div style="margin-left: 10px; clear:both; float:right; white-space: nowrap;">
            <div checkbox="true" name="hidden"></div>
            &nbsp;
            Hidden
        </div>

        <div class="input-block">
            Type
            <span class="required-star">*</span>
            <br />
            <div class="admin_select_block">
                <button type="button" id="button_type_change">change</button>
                <b><span name="item_type">Category</span></b>
                <input type="hidden" value="1" name="item_type" />
            </div>

            <div class="popup-window" id="popup_type_change">
                <div class="popup-window-content">
                    <div style="float:right">
                        <button type="button" name="cancel">X</button>
                    </div>
                    <div style="clear:both;">&nbsp;</div>
                    <select name="select_type_popup" multiple="multiple" size="3">
                        <option value="1">Category</option>
                        <option value="2">Product (big)</option>
                        <option value="3">Product (normal)</option>
                    </select>
                    <br />
                    <button type="button" name="apply_type">Apply</button>
                </div>
            </div>
        </div>

        <div class="input-block">
            Title
            <span class="required-star">*</span>
            <br />
            <input type="text" name="title" />
        </div>

        <div class="input-label-block">
            Label
            <span class="required-star">*</span>
            <br />
            <input type="text" name="label" />
        </div>

        <div class="input-label-block">
            Parent
            <span class="required-star">*</span>
            <br />
            <div class="admin_select_block">
                <button type="button" id="button_parent_change">change</button>
                <b><span name="parent_id">- NO PARENT -</span></b>
                <input type="hidden" value="0" name="parent_id" />
            </div>

            <div class="popup-window" id="popup_parent_change">
                <div class="popup-window-content">
                    <div style="float:right">
                        <button type="button" name="cancel">X</button>
                    </div>
                    <div style="clear:both;">&nbsp;</div>
                    <select name="select_parent_popup" multiple="multiple" size="20">
                        <option value="0">- NO PARENT -</option>
                        <?php

                        if($products){
                            foreach($products as $product){
                                if ($product["type"] == TYPE_CATEGORY){
                                    $selected = ($product["label"] == "catalogue" ? "selected" : "");
                                    echo "<option value='$product[id]' " . $selected . ">$product[title]</option>";
                                }
                            }
                        }

                        ?>
                    </select>
                    <br />
                    <button type="button" name="apply_parent">Apply</button>
                </div>
            </div>
        </div>

        <div class="input-block">
            Price
            <br />
            <input type="text" name="price" value="0" />

            <div class="admin_select_block">
                <button type="button" id="button_currency_change">change</button>
                <b><span name="currency_id">UAH</span></b>
                <input type="hidden" value="0" name="currency_id" />
            </div>

            <div class="popup-window" id="popup_currency_change">
                <div class="popup-window-content">
                    <div style="float:right">
                        <button type="button" name="cancel">X</button>
                    </div>
                    <div style="clear:both;">&nbsp;</div>
                    <select name="select_currency_popup" multiple="multiple" size="3">
                        <?php

                        foreach($currency as $c){
                            echo "<option value='$c[id]'>($c[mark]) $c[name]</option>\n";
                        }

                        ?>
                    </select>
                    <br />
                    <button type="button" name="apply_currency">Apply</button>
                </div>
            </div>

            <input style="width:3em" type="text" name="amount" hint="Amount" />Pcs.
        </div>

        <?php

        if (G::$user->isOrHigher(U_ADMIN)){

        ?>

        <div class="input-block">
            Original marking
            <br />
            <input type="text" name="original_marking" />
        </div>

        <div class="input-block">
            Properties
            <br />
            <input type="text" name="props" hint="property=value,property=value,..." />
        </div>
        <?php

        }

        ?>

        <div class="input-block">
            Brand
            <br />
            <div class="admin_select_block">
                <button type="button" id="button_brand_change">change</button>
                <b><span name="brand_id">Undefined</span></b>
                <input type="hidden" value="0" name="brand_id" />
            </div>

            <div class="popup-window" id="popup_brand_change">
                <div class="popup-window-content">
                    <div style="float:right">
                        <button type="button" name="cancel">X</button>
                    </div>
                    <div style="clear:both;">&nbsp;</div>
                    <select name="select_brand_popup" multiple="multiple" size="10">
                        <option value="0">- UNDEFINED -</option>
                        <?php

                        foreach($brands as $b){
                            echo "<option value='$b->id'>$b->name</option>\n";
                        }

                        ?>
                    </select>
                    <br />
                    <button type="button" name="apply_brand">Apply</button>
                </div>
            </div>
        </div>

    </div>

    <br />

    <!-- meta TAGS - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->
    <div class="marked-block" spoiler="true" title="Meta">
        <h3>Meta tags</h3>

        <div class="input-block">
            robots
            <br />
            <input type="text" name="robots"><br />
            <a href="javascript:setRobots(1)">index,follow</a> : <span class="green-text">Index <b>current page</b> and each <b>contained links</b></span><br />
            <a href="javascript:setRobots(2)"">noindex,follow</a> : <span class="red-text">Don't index <b>current page</b></span><span class="green-text"> but index <b>contained links</b></span><br />
            <a href="javascript:setRobots(3)"">index,nofollow</a> : <span class="green-text">Index <b>current page</b></span> <span class="red-text">and do not index <b>contained links</b></span><br />
            <a href="javascript:setRobots(4)"">noindex,nofollow</a> : <span class="red-text">Do not index neither <b>current page</b> nor <b>contained links</b></span>
        </div>

        <div class="input-block">
            description
            <br />
            <input type="text" name="description" />
        </div>

        <div class="input-block">
            keywords
            <br />
            <input type="text" name="keywords" />
        </div>

        <div class="input-block">
            author
            <br />
            <input type="text" name="author" />
        </div>

        <div class="input-block">
            copyright
            <br />
            <input type="text" name="copyright" />
        </div>

        <div class="input-block">
            author_url
            <br />
            <input type="text" name="author_url" />
        </div>

        <div class="input-block">
            publisher_url
            <br />
            <input type="text" name="publisher_url" />
        </div>

    </div>

    <div class="marked-block" spoiler="true" title="Socials">
        <h3>Socials tags</h3>

        <div class="input-block">
            og:title
            <br />
            <input type="text" name="og_title" />
        </div>

        <div class="input-block">
            og:url
            <br />
            <input type="text" name="og_url" />
        </div>

        <div class="input-block">
            og:image
            <br />
            <input type="text" name="og_image" hint="http://..." />
        </div>

        <div class="input-block">
            og:description
            <br />
            <input type="text" name="og_description" />
        </div>

        <div class="input-block">
            og:site_name
            <br />
            <input type="text" name="og_site_name" />
        </div>

        <div class="input-block">
            og:type
            <br />
            <input type="text" name="og_type" hint="website|article" />
        </div>

        <div class="input-block">
            og:locale
            <br />
            <input type="text" name="og_locale" hint="ru_RU|uk_RU|us_EN|..." />
        </div>

    </div>

    <div class="input-block">
        Content
        <br />

        <noscript>
            <p>
                <strong>CKEditor requires JavaScript to run</strong>. In a browser with no JavaScript
                support, like yours, you should still see the contents (HTML data) and you should
                be able to edit it normally, without a rich editor interface.
            </p>
        </noscript>

        <textarea cols="10" name="content" rows="15"></textarea>

    </div>

    <br />
    <button type="submit" class="button-css-blue">Save</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="reset" class="button-css-blue">Reset</button>

</form>