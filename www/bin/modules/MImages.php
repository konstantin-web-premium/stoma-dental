<?php
include_once("MAdmin.php");

class MImages extends MAdmin implements iAdminModule{
    #override
    protected static  $NAME = "Images";
    #override
    protected $JS_FILES = "mimages.js,file_uploader.js,jquery.ajaxfileupload.js,tablefilter.min.js,cropper.js";

    // labels \ actions
    // public
    public $type;

    public function __construct(){
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

    private function renderNavigation(){
        $str = "[<a href='/admin/images/pages'>Pages</a>]" .
            "&nbsp;&nbsp;&nbsp;&nbsp;".
            "[<a href='/admin/images/c_small'>Catalogue (small)</a>]" .
            "&nbsp;&nbsp;&nbsp;&nbsp;".
            "[<a href='/admin/images/c_medium'>Catalogue (medium)</a>]" .
            "&nbsp;&nbsp;&nbsp;&nbsp;".
            "[<a href='/admin/images/c_large'>Catalogue (large)</a>]" .
            "&nbsp;&nbsp;&nbsp;&nbsp;".
            "[<a href='/admin/images/news'>News</a>]" .
            "&nbsp;&nbsp;&nbsp;&nbsp;".
            "[<a href='/admin/images/articles'>Articles</a>]" .
            "<br />";
        G::addToRender($str, BLOCK_PRE_CONTENT);
    }

    private function renderIndex(){
        $this->renderImagesList();
    }

    private function renderImagesList(){
        G::addToRender("forms/admin_copy_image_form.php", BLOCK_PRE_CONTENT, CODE);
        G::addToRender("admin/mimages_images_table.php", BLOCK_PRE_CONTENT, CODE);
        //G::addToRender("forms/admin_product_edit_form.php", BLOCK_CONTENT, CODE);
        G::addToRender("forms/admin_upload_image_form.php", BLOCK_CONTENT, CODE);
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * implements interface iAdminModule
     */
    public function init($address){
        $this->action = (strlen($address["action"]) ? $address["action"] : "pages");
        G::$pageData->imagesType = $this->action;
        $this->data["content"] = "";
    }

    /**
     * implements interface iAdminModule
     */
    #override
    public function getData(){
        $this->renderNavigation();
        $this->renderImagesList();
        return $this->data;
    }

    public function getAction(){
        return $this->action;
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