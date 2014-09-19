<?php
include_once("utils/RenderUtils.php");

class UserData{
    const ERROR_LOGIN_GLYPHS = "error_login_glyphs";
    const ERROR_LOGIN_EXISTS = "error_login_exists";
    const ERROR_PASSWORD_GLYPHS = "error_password_glyphs";
    const ERROR_PASSWORD_LENGTH = "error_password_length";
    const ERROR_PASSWORD_MISMATCH = "error_password_mismatch";
    const ERROR_CAPTCHA_MISMATCH = "error_captcha_mismatch";
    const ERROR_NAME_LENGTH = "error_name_length";

    // Cookies keys
    const C_USER_LOGIN = "user_login";
    const C_HASH = "hash";

    const DEFAULT_SENIORITY = 4;

    private $errors;
    private $errors_key;

    public $data;

    public function __construct(){
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * @return string - random string [a-z,A-Z,0-9]
     */
    private function generateCode($length = 6) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }

    /** Checks if user with $login exists
     * @param $login
     * @return bool
     */
    private function user_exists($login){
        G::logMessage(" login = $login");
        if (strlen($login) <= 0)
        {
            return false;
        }
        $query = G::$db->query("SELECT * FROM ".TABLE_USERS." WHERE login='$login' LIMIT 1");
        $users = $query->fetchAll();

        if(count($users) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $login
     * @return array - errors
     */
    private function validate_login($login){
        $errors = array();

        // glyphs
        if(strlen(LOGIN_REG_EXP) > 0 && !preg_match(LOGIN_REG_EXP, $login))
        {
            $errors[] = $this->getErrorText(self::ERROR_LOGIN_GLYPHS);
        }else
        if($this->user_exists($login))
        {
            $errors[] = $this->getErrorText(self::ERROR_LOGIN_EXISTS);
        }

        if(count($errors)){
            $this->errors_key["login"] = true;
        }

        return $errors;
    }

    /**
     * @param $password
     * @return array - errors
     */
    private function validate_password($password, $confirm){
        $errors = array();
        // confirm
        if ($password !== $confirm){
            $errors[] = $this->getErrorText(self::ERROR_PASSWORD_MISMATCH);
            $this->errors_key["password_confirm"] = true;
        }
        // glyphs
        if(strlen(PASSWORD_REG_EXP) > 0 && !preg_match(PASSWORD_REG_EXP, $password))
        {
            $errors[] = $this->getErrorText(self::ERROR_PASSWORD_GLYPHS);
        }
        // length
        if(strlen($password) < PASSWORD_MIN_LENGTH or strlen($password) > PASSWORD_MAX_LENGTH)
        {
            $errors[] = $this->getErrorText(self::ERROR_PASSWORD_LENGTH);
        }

        if(count($errors)){
            $this->errors_key["password"] = true;
        }

        return $errors;
    }

    private function validate_captcha($captcha){
        $errors = array();
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !== $captcha){
            $errors[] = $this->getErrorText(self::ERROR_CAPTCHA_MISMATCH);
        }

        if(count($errors)){
            $this->errors_key["keystring"] = true;
        }

        return $errors;
    }

    private function validate_name($name){
        $errors = array();
        // length (as login)
        if(strlen($name) < NAME_MIN_LENGTH)
        {
            $errors[] = $this->getErrorText(self::ERROR_NAME_LENGTH);
        }

        if(count($errors)){
            $this->errors_key["username"] = true;
        }

        return $errors;
    }

    private function getErrorText($error){
        $str = G::$language->getText("errors", $error);
        return (strlen($str) > 0 ? $str : "Error $error");
    }

    private function resetCookies(){
        setcookie(self::C_USER_LOGIN, "", time() - 3600*24*30*12, "/");
        setcookie(self::C_HASH, "", time() - 3600*24*30*12, "/");
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------

    /** Login User
     * @param $login
     * @param $password
     * @return bool
     */
    public function loginUser($login, $password){
        $login = G::$db->quote($login);
        $query = G::$db->query("SELECT id, login, password FROM users WHERE login=$login LIMIT 1");
        $data = $query->fetch(); $query->closeCursor();
        $id = $data["id"];
        $login = $data["login"];
        if($data['password'] !== md5(md5($password)))
        {
            return false;
        }

        $hash = md5($this->generateCode(10));

        $ip = "INET_ATON('$_SERVER[REMOTE_ADDR]')";
        $sid = session_id();

        G::$db->exec("UPDATE users SET hash='$hash', ip=$ip, sid='$sid' WHERE id='$id'");

        $cookie_expire = time() + intval(COOKIE_EXPIRE_TIME);
        setcookie(self::C_HASH, $hash, $cookie_expire);
        setcookie(self::C_USER_LOGIN, $login, $cookie_expire);

        return true;
    }

    /** Register User
     * @param $data array from $_POST
     * @return bool
     */
    public function registerUser($data, $ignore_captcha = false){
        $this->errors = array();
        $this->errors_key = array();
        $login              = $data["login"];
        $password           = trim($data["password"]);
        $password_confirm   = trim($data["password_confirm"]);
        $name               = $data["username"];
        $organization       = $data["organization"];
        $tel                = $data["tel"];
        $captcha            = $data["keystring"];
        // ANTI BOT
        $nonbotable         = $data["nonbotable"];  // if NOT SET means BOT
        $email              = $data["email"];       // if NOT empty means BOT

        $this->errors = array_merge(
            $this->errors,
            $this->validate_name($name),
            $this->validate_login($login),
            $this->validate_password($password, $password_confirm),
            ($ignore_captcha ? array() : $this->validate_captcha($captcha))
        );


        if (count($this->errors) <= 0)
        {
            $password      = md5(md5($password));
            $login         = G::$db->quote($data["login"]);
            $name          = G::$db->quote($data["username"]);
            $organization  = G::$db->quote($data["organization"]);
            $tel           = G::$db->quote($data["tel"]);


            $result = G::$db->exec("INSERT INTO ".TABLE_USERS." SET login=$login, password='$password', name=$name");
            //header("Location: login.php"); exit();
            if (!$result)
            {
                $this->errors[] = "[" . G::$db->errorCode(). "] " . print_r(G::$db->errorInfo(), true);
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * load Cookies data
     */
    public function loadFromCookies(){
        $login = $_COOKIE['user_login'];
        $hash = $_COOKIE['hash'];
        if (!preg_match(LOGIN_REG_EXP, $login))
        {
            $this->errors[] = "Invalid User LOGIN";
            $this->resetCookies();
            unset($this->data);
            return;
        }

        if (isset($hash)){
            $query = G::$db->query("SELECT *, INET_NTOA(ip) FROM ".TABLE_USERS." WHERE login = '$login' LIMIT 1");
            $userdata = $query->fetch(); $query->closeCursor();
            if($userdata['hash'] === $hash
                &&
                $userdata['sid'] === session_id()
            ){
                // OK
                $this->data = $userdata;
            }
            else
            {
                $this->resetCookies();
                $this->errors[] = "nonauthorized: Invalid cookies";
            }
        }else{
            $this->errors[] = "NO COOKIES";
            unset($this->data);
        }
    }

    public function getUserDataById($id){
        $query = G::$db->query("SELECT *, INET_NTOA(ip) FROM " . TABLE_USERS . " WHERE id = '$id' LIMIT 1");
        $userdata = $query->fetch(); $query->closeCursor();
        return $userdata;
    }

    public function logout(){
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie(self::C_HASH, "", time() - 3600*24*30*12, "/");
        session_destroy();
    }

    public function isOrHigher($required_s){
        $required_s = intval($required_s);

        $current_s = (isset($this->data["seniority"]) ? $this->data["seniority"] : self::DEFAULT_SENIORITY);

        if ($current_s <= $required_s && $required_s > 0)
        {
            return true;
        }
        return false;
    }

    public function isAuthorized(){
        return isset($this->data);
    }

    public function getErrors($rendered = false){
        $errors = array();

        if (!$rendered){
            $errors = $this->errors;
        }
        else
        {
            foreach($this->errors as $error){
                $errors[] = RenderUtils::renderError($error);
            }
        }

        return $errors;
    }

    public function getErrorsKey(){
        return $this->errors_key;
    }

}
?>