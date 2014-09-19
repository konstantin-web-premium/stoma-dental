<?php
include_once("MAdmin.php");

class MCatalog extends MAdmin implements iAdminModule{
    #override
    protected static  $NAME = "Catalogue";
    #override
    protected $JS_FILES = "mcatalog.js,ckeditor/ckeditor.js,ckeditor/adapters/jquery.js,item_form.js,item_preview.js,file_uploader.js,jquery.ajaxfileupload.js,tablefilter.min.js,cropper.js";

    // labels \ actions
    const PRODUCT = "product";

    protected $categoriesTree;

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

    private function renderIndex(){
        $this->renderProductsList();
    }

    private function renderCategoriesList(){
        G::addToRender("admin/mcatalog_categories.php", BLOCK_CONTENT, CODE);
    }

    private function renderProductsList(){
        G::addToRender("admin/mcatalog_products_table.php", BLOCK_PRE_CONTENT, CODE);
        G::addToRender("forms/admin_product_edit_form.php", BLOCK_CONTENT, CODE);
        G::addToRender("admin/admin_product_preview.php", BLOCK_SIDEBAR_LEFT, CODE);
    }

    private function renderEditProductForm(){
       // G::addToRender("admin/mcatalog_products_table.php", BLOCK_CONTENT, CODE);
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * implements interface iAdminModule
     */
    public function init($address){
        $this->action = $address["action"];
        $this->data["content"] = "";
        //$this->renderNavigation();
        $this->categoriesTree = CatalogueUtils::getCategoryTree();
    }

    /**
     * implements interface iAdminModule
     */
    #override
    public function getData(){
        switch($this->action){
            default:
                $this->renderIndex();
                break;
            case self::PRODUCT:
                $this->renderEditProductForm();
                break;
        }
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