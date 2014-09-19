<?php
include_once "BaseProductProperty.php";

class RangeProperty extends BaseProductProperty{
    #override
    const DEFAULT_VALUE = "1";

    private $min = 0;
    private $max = 1;
    private $from = 0;
    private $to = 1;

//

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    public function __construct($value, $base_prop_data){
        // RANGE
        if (count($this->values) > 0){
            $this->min = $this->values[0];
            $this->max = isset($this->values[1]) ? $this->values[1] : $this->max;
        }

        parent::__construct($value, $base_prop_data);

        // VALUES
        $range = explode("-", $this->value);
        if (count($range) > 1){
            $this->from = $range[0];
            $this->to = $range[1];
        }else{
            $this->from = $this->value;
            $this->to = $this->value;
        }

    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    #override
    public function render(){
        return ($this->from == $this->to ? $this->from : $this->from . " - " . $this->to);
    }


}
?>