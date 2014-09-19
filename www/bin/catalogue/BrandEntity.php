<?php
class BrandEntity{
    const SIZE_SMALL = "small";
    const SIZE_MEDIUM = "medium";
    const SIZE_LARGE = "large";

    public $id;
    public $order;
    public $hidden;
    public $label;
    public $name;
    public $full_name;
    public $description;
    public $url;
    private $logo;

    private $country_data;

    public function __construct($data){
        $this->id = intval($data["id"]);
        $this->order = intval($data["order"]);
        $this->hidden = (intval($data["hidden"]) == 1 ? true : false);
        $this->label = $data["label"];
        $this->name = (string) $data["name"];
        $this->full_name = (string) $data["full_name"];
        // country data
        $this->country_data = array(
            "id" => $data["country_id"],
            "code" => $data["country_code"],
            "name" => $data["country_short_name"],
            "full_name" => $data["country_full_name"]
        );
        //
        $this->description = $data["description"];
        $this->url = $data["url"];

        $this->logo = "images/brands/" . $this->id . "jpg";
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function getLogoUrl($size){
        $img_url = PATH_BRANDS_IMAGES;
        switch($size){
            case self::SIZE_SMALL:
                $img_url .= "small_";
                break;
            case self::SIZE_MEDIUM:
                $img_url .= "medium_";
                break;
            case self::SIZE_LARGE:
                $img_url .= "large_";
                break;
        }
        $img_url .= $this->id . ".png";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $img_url)){
            $img_url = "";
        }

        return $img_url;
    }

    public function getCountry($key = "name"){
        return $this->country_data[$key];
    }
}
?>