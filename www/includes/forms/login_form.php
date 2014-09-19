<?php
$text_login = G::$language->getText("common", "login_text");
$text_password = G::$language->getText("common", "password_text");
$text_enter = G::$language->getText("common", "enter_text");
?>
<div class="login-form">
<form method="POST" action="/login">
    <input type="text" name="login" autofocus="true" max="30" hint="<?php echo $text_login; ?>" />
    <br />
    <input type="password" autocomplete="off" name="password" max="32" hint="<?php echo $text_password; ?>" />
    <br />
    <div class="login-submit-block">
        <button class="button-css-blue" name="submit" type="submit" />Submit</button>
        <!--
            <a href="/login_reset" class="login-reset-link">Forgot password?</a><br />
            <a href="/register" class="login-reset-link">Register</a>
            -->
    </div>
</form>
</div>
