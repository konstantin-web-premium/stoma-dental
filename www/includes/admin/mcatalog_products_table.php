<?php
$products = PageUtils::getItemsList("catalogue", true);
?>


<!--div class="cropper-container">
    <img class="cropper" src="/images/catalogue/large/durr-trio.jpg">
    <div class="extra-preview">
    </div>
</div -->


<button class="button-css-blue" id="table_clean_filters">Clear filters</button>
<span id="table_rowcount"></span>
<button class="button-css-blue" id="button_minimize_table" style="float: right;">_</button>
<button class="button-css-blue" id="button_create_new" style="float: right; margin-right:5px;">Create NEW</button>
<div class="semi-spoiler" height="200">
    <table id="table_products_list" class="nodes_list_table">
        <thead>
            <tr>
                <th width="30px">id</th>
                <th width="60px" filter-type='ddl'>type</th>
                <th filter-type='ddl'>brand_id</th>
                <th>marking</th>
                <th>image</th>
                <th>label</th>
                <th width="30%">title</th>
                <th width="60px">price</th>
                <th width="60px">amount</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach($products as $product){

        //DEBUG
        /*
        if ($product["id"] == 3){
            G::logMessage("IN DB 11 look like >> '" . $product["title"] ."'");
            $res = mb_detect_encoding($product["title"], array("UTF-8", "WINDOWS-1251"));
            $resUTF = iconv("WINDOWS-1251", "UTF-8", $product["title"]);
            $resWIN = iconv("UTF-8", "WINDOWS-1251", $product["title"]);
            G::logMessage("mb_detect_encoding('" . $product["title"] . "') = " . $res . "; >> to UTF=" . $resUTF . "; to WIN=" . $resWIN . ";");
        }
        */
        //

        echo RenderUtils::renderNodeTableRow($product);
    }
    ?>
        </tbody>
    </table>
</div>