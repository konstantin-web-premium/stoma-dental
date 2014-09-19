<?php
include_once($_SERVER["DOCUMENT_ROOT"] . PATH_BIN."interfaces/iAdminModule.php");
include_once($_SERVER["DOCUMENT_ROOT"] . PATH_BIN."utils/AdminUtils.php");
include_once($_SERVER["DOCUMENT_ROOT"] . PATH_BIN."utils/PageUtils.php");

class MAdmin implements iAdminModule{
    // self
    const ERROR_ABSTARCT = "Abstract method impelmentation! Use override methods instead!";
    // static (abstract)
    protected static $NAME = "DefaultModuleName";

    protected $JS_FILES = "";

    protected $data = array();
    protected $errors = array();
    protected $action;

    public function __construct(){
        # not used yet
    }

    /**
     * implements interface iAdminModule
     */
    public static function getTitle(){
        return self::$NAME;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * implements interface iAdminModule
     */
    public function init($address){
        throw new Exception(self::ERROR_ABSTARCT);
    }

    /**
     * implements interface iAdminModule
     */
    public function getData(){
        throw new Exception(ERROR_ABSTARCT);
    }

    /**
     * implements interface iAdminModule
     */
    public function getProp($prop){
        if(isset($this->$prop)){
            return $this->$prop;
        }
        return null;
    }

    /**
     * implements interface iAdminModule
     */
    public function getRequiredJSFilenames(){
        $arr = explode(",", $this->JS_FILES);
        return $arr;
    }

    public function __toString(){
        return "[Admin Module " . self::$NAME . "]";
    }


}
?>