<?php
include_once "interfaces/iAdminModule.php";

class AdminModulesManager{

    private $currentModule;
    private $moduleClassName;
    private $moduleLabel;

    public $requiredJSFiles;
    public $requiredCSSFiles;

    public function __construct($moduleLabel){
        $this->moduleLabel = $moduleLabel;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private function initModule(){
        if (G::$user->isOrHigher(U_MODERATOR)
            &&
            (!isset(G::$modules) || !is_array(G::$modules) || !count(G::$modules))
        ){
            G::fatalError("AdminModulesManager::initOvertopBlock() : Modules' config had not been inited yet!");
        }

        if (!$this->currentModule){
            if (isset(G::$modules[$this->moduleLabel]))
            {
                $this->moduleClassName = $className = G::$modules[$this->moduleLabel];
                $adminModule = new $className;
                if (!($adminModule instanceof iAdminModule)){
                    G::fatalError("PageData::getAdminPageItem() : Admin module (".G::$modules[$this->moduleLabel]." => ".$className.") does not implement iAdminModule correct.");
                }
            }
            $this->currentModule = $adminModule;
        }
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function init(){
        // prerender visual (overtop) block
        G::addToRender("blocks/overtop.php", BLOCK_OVERTOP, CODE);

        // required CSS
        $this->requiredCSSFiles = array();
        $this->requiredCSSFiles[] = CSS_ADMIN_FILE;
        $this->requiredCSSFiles[] = "cropper.css";

        // init MODULE
        $this->initModule();

        // required JS
        $this->requiredJSFiles = array();
        if ($this->getCurrent()){
            $filenames = $this->getCurrent()->getRequiredJSFilenames();
            foreach($filenames as $filename){
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . PATH_SCRIPTS . $filename)){
                    $this->requiredJSFiles[] = $filename;
                }
            }
        }
    }

    public function getCurrent(){
        if (!isset($this->currentModule)){
            $this->initModule();
        }

        return $this->currentModule;
    }
}
?>