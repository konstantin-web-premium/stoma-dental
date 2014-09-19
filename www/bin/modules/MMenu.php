<?php
include_once("MAdmin.php");

class MMenu extends MAdmin implements iAdminModule{
    #override
    protected static $NAME = "Menu";

    private $menuTitle;

    /**
     * implements interface iAdminModule
     */
    public static function getTitle(){
        return self::$NAME;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

    private function renderLeftBlock(){
        $content = "<div style='border:1px solid #000; padding:5px; width:30%; clear:both; float:left;'>\n";
        $menus = AdminUtils::getMenuList();
        foreach($menus as $id => $title){
            if(!isset($this->menuTitle)){
                $this->menuTitle = $title;
            }
            $content .= "<a href='/admin/menu/$title'>$title</a> <br />\n";
        }

        $content .= "</div>\n";
        $this->data["content"] .= $content;
    }

    private function renderMiddleBlock(){
        global $pageData;
        $content = "<div style='border:1px solid #000; padding:5px; width:30%; float:left;'>\n";
        $menu = G::$pageData->getMenu($this->menuTitle);
        foreach($menu as $m){
            $content .= "- $m[content] <br />\n";
        }
        $content .= "</div>\n";
        $this->data["content"] .= $content;
    }

    private function renderRightBlock(){
        $content = "<div style='border:1px solid #000; padding:5px; width:30%; float:left;'>\n";
        $items = AdminUtils::getMenuItemsList();
        foreach($items as $id => $title){
            $content .= "- $title <br />\n";
        }
        $content .= "</div>\n";
        $this->data["content"] .= $content;
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * implements interface iAdminModule
     */
    #override
    public function init($address){
        $this->menuTitle = $address[2];
        $this->data["content"] = "";
    }

    /**
     * implements interface iAdminModule
     */
    #override
    public function getData(){
        $this->renderLeftBlock();
        $this->renderMiddleBlock();
        $this->renderRightBlock();
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