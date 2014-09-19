<?php
include_once ROOT . "/bin/utils/CatalogueUtils.php";
include_once "props/ProductProperty.php";
include_once "props/ColorProperty.php";
include_once "props/ExistProperty.php";
include_once "props/CompatibilityProperty.php";
include_once "props/RangeProperty.php";

class PropsManager{
    private $props_data;

    public function __construct(){
        $this->init();
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

    private function init(){
        $this->props_data = CatalogueUtils::loadPropsData();
    }

    private function getPropDataByName($name){
        foreach($this->props_data as $data){
            if ($data["name"] == $name){
                return $data;
            }
        }
        return null;
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function createProperty($name, $value){
        $base_prop_data = $this->getPropDataByName($name);
        if ($base_prop_data){
            switch($base_prop_data["type"]){
                case PROP_TYPE_SWITCH:
                case PROP_TYPE_SWITCH_STRICT:
                    return new ProductProperty($value, $base_prop_data);
                case PROP_TYPE_COLOR:
                    return new ColorProperty($value, $base_prop_data);
                case PROP_TYPE_EXIST:
                    return new ExistProperty($value, $base_prop_data);
                case PROP_TYPE_RANGE:
                    G::logMessage("CATCH RANGE TYPE");
                    return new RangeProperty($value, $base_prop_data);
                default:
                    return BaseProductProperty($value, $base_prop_data);
            }
        }
    }

}
?>