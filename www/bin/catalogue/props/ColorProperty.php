<?php
include_once "BaseProductProperty.php";

class ColorProperty extends BaseProductProperty{
    #override
    private $DEFAULT_VALUE = "none";

//

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    #override
    private function getDefaultValue(){
        return $this->$DEFAULT_VALUE;
    }

    private function colorToHex($color){
        switch(strtolower($color)){
            default:
                return "transparent";
            case "black":
                return "#000000";
            case "red":
                return "#990000";
            case "green":
                return "#009900";
            case "blue":
                return "#336699";
        }
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function render(){
        $color = $this->colorToHex($this->value);
        $result = "<div style='width:30px; height:30px; border: 1px solid #e5e5e5; background-color: $color;'></div>";
        return $result;
    }

}
?>