<?php
$text_news = G::$language->getText("common", "news_text");

$news = G::$pageData->getNewsList();
?>
<h1><?php echo $text_news;?></h1>
<?php

$total = 0;
$max_total = 5;
foreach($news as $item){
    if ($item["type"] != NEWS_TYPE_NEWS){
        continue;
    }

    $img_url = PATH_NEWS_IMAGES . $item["image"] . ".jpg";

    ?>
    <div class="news-item">

        <a href="/news/<?php echo $item["label"]?>">
            <img src="<?php echo $img_url; ?>" alt="<?php echo $item["short"]; ?>" />
        </a>
        <div class="text-block">
            <a href="/news/<?php echo $item["label"]?>"><?php echo $item["short"];?></a>
        </div>
    </div>
    <?php
    $total++;
    if ($total >= $max_total){
        break;
    }
}

?>