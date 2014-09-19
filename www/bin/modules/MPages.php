<?php
include_once("MAdmin.php");

class MPages extends MAdmin implements iAdminModule{
    #override
    protected static $NAME = "Pages";
    #override
    protected $JS_FILES = "mpages.js,item_form.js,ckeditor/ckeditor.js,ckeditor/adapters/jquery.js";

    private $page;

    /**
     * implements interface iAdminModule
     */
    public static function getTitle(){
        return self::$NAME;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

    private function renderLocalMenu(){
        G::addToRender("admin/mpages_tabs.php", BLOCK_CONTENT, CODE);
    }

    private function renderIndex(){
        $this->data["content"] = "";
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * implements interface iAdminModule
     */
    #override
    public function init($address){
        array_shift($address);
        array_shift($address);
        $this->page = implode($address);
        $this->data["content"] = "";
    }

    /**
     * implements interface iAdminModule
     */
    #override
    public function getData(){
        $this->renderLocalMenu();
        return $this->data;
    }

    /**
     * implements interface iAdminModule
     */
    #override
    public function getRequiredJSFilenames(){
        $arr = explode(",", $this->JS_FILES);
        return $arr;
    }

}
?>