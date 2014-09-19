<?php
class Language{
    private $data;
    private $id = 1;

    public $charset;
    public $name;
    public $code;
    public $og_code;

    public function __construct(){
        $this->initDefaultData();
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private function initDefaultData(){
        $this->id = 1;
        $this->name = "English";
        $this->code = "en";
        $this->og_code = "en_US";
        $this->icon = "";
        $this->charset = "UTF-8";

    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function setLanguage($langId){
        $data = array();

        $query = G::$db->query("SELECT * FROM " . TABLE_LANGUAGE . " WHERE id='$langId' LIMIT 1");
        $row = $query->fetch(); $query->closeCursor();
        if (!$query || !$row)
        {
            return;
        }
        $this->id = $langId;
        $this->name = $row['name'];
        $this->code = $row['code'];
        $this->og_code = $row['og_code'];
        $this->icon = $row['icon'];
        $this->charset = $row['charset'];

        $query = G::$db->query("SELECT * FROM " . TABLE_LOCALIZATION . " WHERE language_id='$langId'");
        while ($row = $query->fetch()){
            $category = $row["category"];
            $label = $row["label"];
            $value = $row["value"];
            if (!isset($data[$category])){
                $data[$category] = array();
            }
            $data [$category] [$label] = $value;
        }

        $this->data = $data;
    }

    public function getText($category, $item){
        return $this->data[$category] [$item];
    }
}
?>