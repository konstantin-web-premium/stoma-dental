<?php
class BaseProductProperty{
    const DEFAULT_VALUE = "n/a";

    public $value;
    public $name;
    public $title;
    public $hidden = false;
    public $after_choose = false;

    public $values;

//

    public function __construct($value, $base_prop_data){
        $this->value = $value;
        $this->name = $base_prop_data["name"];
        $this->title = $base_prop_data["title"];
        $this->values = explode(";", $base_prop_data["values"]);
        $this->hidden = ($base_prop_data["hidden"] == 1);
        $this->after_choose = ($base_prop_data["after_choose"] == 1);
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private function getDefaultValue(){
        return static::DEFAULT_VALUE;
    }


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function render(){
        $str = "";
        if (!$this->after_choose){
            $str = $this->value . "";
        }else if($this->values && count($this->values) > 0){
            $str = "<select >";
            foreach($this->values as $value){
                $str .= "<option value='$value'>$value</option>";
            }
            $str .= "</select>";
        }
        return $str;
    }

}
?>