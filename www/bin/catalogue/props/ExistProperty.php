<?php
include_once "BaseProductProperty.php";

class ExistProperty extends BaseProductProperty{
    #override
    const DEFAULT_VALUE = "0";

    private $value_0_str = "N";
    private $value_1_str = "Y";

//

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    public function __construct($value, $base_prop_data){
        parent::__construct($value, $base_prop_data);
        $this->value_0_str = G::$language->getText("common", "notexist_text");
        $this->value_1_str = G::$language->getText("common", "exist_text");
        if (count($this->values) > 1){
            $this->value_0_str = $this->values[0];
            $this->value_1_str = $this->values[1];
        }

    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    #override
    public function render(){
        switch($this->value){
            default:
            case "0":
                return $this->value_0_str;
            case "1":
                return $this->value_1_str;
            case "2":
                return "n/a";
        }
    }

}
?>