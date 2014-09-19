<?php
// TODO
/*
    In cart 2 items
    <a href="#">Make order</a>
    Total: 1 234 234 uah
*/
?>

<div class="s-cart-container">
    <?php
    if(G::$user->isAuthorized()){
        echo G::$user->data["name"].
            "<a href='/logout'>&nbsp;" . G::$language->getText("common", "exit_text") . "&nbsp;</a>";

    }else{
        include $_SERVER["DOCUMENT_ROOT"] . PATH_INCLUDES . "/forms/login_form.php";
    }
    ?>
</div>