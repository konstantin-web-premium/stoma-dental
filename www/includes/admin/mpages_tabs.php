<?php
// MPAGES
$pages = G::$pageData->getItemsList("pages");
//$articles = G::$pageData->getItemsList("articles");
$products = G::$pageData->getItemsList("catalogue");
?>
<div class="tab-section">
    <ul class="tabs">
        <li class="current">Pages</li>
        <li>Articles</li>
    </ul>
    <div class="tab-box visible">
        <select id="select_page">
            <option value="0">-Select page-</option>
            <?php

            if(count($pages)){
                foreach($pages as $item){
                    echo "<option value=\"$item[label]\">(" . $item['label'] . ") " . $item['title'] . "</option>";
                }
            }

            ?>
        </select>
        <button id="button_load_page" disabled="disabled" class="button-css-blue">Load</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button id="button_create_page" class="button-css-blue">Create NEW</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button id="button_delete_page" disabled="disabled" style="float:right" class="button-css-red">Delete Page</button>

        <hr />
        <?php

        include ROOT . PATH_INCLUDES . "forms/admin_page_edit_form.php";

        ?>
    </div>

    <!-- ARTICLES --------------------------------------------------- -->
    <div class="tab-box">
        <select id="select_article">
            <option value="0">-Select article-</option>
            <?php

            if(count($articles)){
                foreach($articles as $item){
                    echo "<option value=\"$item[label]\">(" . $item['label'] . ") " . $item['title'] . "</option>";
                }
            }

            ?>
        </select>
        <button id="button_load_article" disabled="disabled" class="button-css-blue">Load</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button id="button_create_article" class="button-css-blue">Create NEW</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button id="button_delete_article" disabled="disabled" style="float:right" class="button-css-red">Delete Article</button>

        <hr />
        <?php

        //include ROOT . PATH_INCLUDES . "forms/admin_artcle_edit_form.php";

        ?>
    </div>
</div><!-- .section -->