<?php
$list = G::$pageData->data["articles"];

if (isset($list)){
    $len = count($list);

    if ($len){

        echo "<ul class='block-list-vertical'>";

        for ($i = 0; $i < $len; $i++){
            $ar = $list[$i];
            $image_url = "/images/articles/$ar[image].jpg";
            $title = $ar["title"];
            $href = "/article/$ar[label]";
            $short = $ar["short"];
            ?>
            <li class="article-list-item">
                <img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>" />
                <a href="<?php echo $href . "/";?>"><?php echo $title; ?></a>
                <p class="description">
                    <?php echo $short; ?>
                </p>
            </li>
            <?php

        }

        echo "</ul>";

        ?>
        <span class="dark-grey-text">ѕо вопросу размещени€ статей на сайте обращайтесь по номеру телефона (044) 22-33-999</span>
        <?php

    }else{
        include("no_articles.php");
    }

}
?>