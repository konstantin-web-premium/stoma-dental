<?php
include_once "BaseProductProperty.php";

class CompatibilityProperty extends BaseProductProperty{
    public $hidden = true;
    #override
    private $DEFAULT_VALUE = "";

//

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    #override
    private function getDefaultValue(){
        return $this->$DEFAULT_VALUE;
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    #override
    public function render(){
        $result = "";
        switch($this->value){
            default:
            case "0":
                return G::$language->getText("common", "notexist_text");
            case "1":
                return G::$language->getText("common", "exist_text");
            case "2":
                return "n/a";
        }
    }

}
?>