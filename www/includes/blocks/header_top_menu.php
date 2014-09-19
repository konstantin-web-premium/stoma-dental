<?php
$top_menu = G::$pageData->getMenu("top_menu");
$text_welcome = G::$language->getText("common" , "welcome_text");
$text_exit = G::$language->getText("common" , "exit_text");
?>
<div class="top-menu">
    <?php
    if ($top_menu && !G::$user->isAuthorized()){
        foreach($top_menu as $menu){
            $title = "\" title=\"".(isset($menu["title"]) ? $menu["title"] : "");
            ?>
            <a href="<?php echo $menu["url"].$title; ?>"><?php echo $menu["content"]; ?></a>
            <?php
        }
    }else{
        echo "$text_welcome, " . G::$user->data["name"] . " [<a style='margin:0;' href='/logout'>$text_exit</a>]";
    }
    ?>
</div>
