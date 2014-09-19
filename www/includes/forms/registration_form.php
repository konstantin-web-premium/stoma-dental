<?php
$text_registration = G::$language->getText("common", "registration_text");
$errors_key = G::$user->getErrorsKey();

$login              = $_POST["login"];
$password           = $_POST["password"];
$password_confirm   = $_POST["password_confirm"];
$name               = $_POST["username"];
$organization       = $_POST["organization"];
$tel                = $_POST["tel"];

$errors             = $_POST["errors"];

$captcha_img_src = "/external/kcaptcha/index.php?" . session_name() . "=" . session_id();
?>
<div class="registration-form">
<h3><?php echo $text_registration; ?></h3>
<form method="POST" action="/register" id="reg_form">

    <div class="input-block">
        Name
        <span class="required-star"><sup>*</sup></span>
        <br />
        <input type="text" name="username" error="<?php echo $errors_key["username"]; ?>" maxlength="32" value="<?php echo $name; ?>" />
    </div>

    <div class="input-block">
        E-mail
        <span class="required-star"><sup>*</sup></span>
        <br />
        <input type="text" name="login" error="<?php echo $errors_key["login"]; ?>" value="<?php echo $login; ?>" />
    </div>

    <div class="marked-block">

    <div class="input-block">
        Password
        <span class="required-star"><sup>*</sup></span>
        <br />
        <input type="password" autocomplete="off" class="input-text" name="password" error="<?php echo $errors_key["password"]; ?>" maxlength="32" value="<?php echo $password; ?>" />
    </div>

    <div class="input-block">
        Confirm password
        <span class="required-star"><sup>*</sup></span>
        <br />
        <input type="password" autocomplete="off" class="input-text" name="password_confirm" error="<?php echo $errors_key["password_confirm"]; ?>" maxlength="32" />
    </div>

    </div>

    <div class="input-block">
        Organization
        <br />
        <input type="text" class="input-text" name="organization" maxlength="32" value="<?php echo $organization; ?>" />
    </div>

    <div class="input-block">
        Tel
        <br />
        <input type="text" class="input-text" name="tel" maxlength="32" hint="+38 (000) 000-00-00" value="<?php echo $tel; ?>" />
    </div>

    <div class="marked-block" id="captcha_block">

    <div class="input-block">
        Captcha
        <span class="required-star"><sup>*</sup></span>
        <br />
        <input type="text" autocomplete="off" class="input-text" name="keystring" error="<?php echo $errors_key["captcha"]; ?>" />
    </div>

    <div class="captcha-block">
        <img id="captcha_image" src="<?php echo $captcha_img_src; ?>" />
        <a href="javascript:changeCaptcha()">Change</a>
    </div>

    </div>

    <div class="input-botable-block">
        <br />
        <input type="text" autocomplete="off" name="email" />
    </div>

    <button class="button-css-blue" name="submit" type="submit">Submit</button>
</form>
</div>

<script>
    initRegForm();
</script>
