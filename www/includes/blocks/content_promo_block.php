<br />
<?php
$promos = G::$pageData->getPromoList();
echo "<h1>" . G::$language->getText("common", "promo_text") . "</h1>";
?>
<div class="promo-container">
        <?php
        foreach($promos as $promo){
            $img_url = PATH_NEWS_IMAGES . $promo["image"] . ".jpg";
            if (!file_exists(ROOT . $img_url)){
                $img_url = PATH_NEWS_IMAGES . "default_promo.jpg";
            }
            $short = $promo["short"];
            echo "<div class='promo-block'>" .
                "<a href='#'><img src=\"$img_url\" class=\"promo-image\" /></a>" .
                "<div class='text-block'><a href='#'>$short</a></div>" .
                "</div>\n";
        }
        ?>
</div>