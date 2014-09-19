<?php
$text_catalogue = G::$language->getText("common", "catalogue_link_text");
$middle_menu = G::$pageData->getMenu("middle_menu");

?>
<div class="middle-menu">
    <a href="/catalogue/" class="highlight"><img src="/images/header/catalogue_icon.jpg" alt="<?php echo $text_catalogue;?>" /><?php echo $text_catalogue;?></a>

    <?php
    for($i = 0; $i < count($middle_menu); $i++){
        $menu = $middle_menu[$i];
        $title = "\" title=\"".(isset($menu["title"]) ? $menu["title"] : "");
        ?>

        <a href="<?php echo $menu["url"].$title; ?>"><?php echo $menu["content"]; ?></a>

        <?php
    }
    ?>
</div>
