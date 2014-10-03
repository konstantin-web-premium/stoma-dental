<?php
$text_promo = G::$language->getText("common", "promo_text");

$items = G::$pageData->getNewsList();
if (count($items)){

    ?>
    <h1><?php echo $text_promo;?></h1>
    <?php

    foreach($items as $item){
        if ($item["type"] != NEWS_TYPE_PROMO){
            continue;
        }

        $img_url = PATH_NEWS_IMAGES . $item["image"] . ".jpg";
        if (!file_exists(ROOT . $img_url)){
            continue;
        }

        ?>
        <div class="news-item">
            <a href="/news/<?php echo $item["label"]?>"><img src="<?php echo $img_url; ?>" alt="<?php echo $item["short"]; ?>" /></a>
            <div class="text-block">
                <a href="/news/<?php echo $item["label"]?>"><?php echo $item["short"];?></a>
            </div>
        </div>
        <?php

    }
}

?>
