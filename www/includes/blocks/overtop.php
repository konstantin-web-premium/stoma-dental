&nbsp;
<?php
foreach(G::$modules as $key => $value){
    $title = call_user_func(array($value, "getTitle"));
    ?>

    <a href="<?php echo "/admin/".$key; ?>"><?php echo $title; ?></a>
<?php
}

?>

<div class="admin-tech-data">
    <?php
    if (G::$user->isOrHigher(U_MODERATOR)){
        echo "Engine v." . WEBSITE_VERSION . ";&nbsp;&nbsp;&nbsp;&nbsp;PHP v." . phpversion();
    }
    ?>
</div>
