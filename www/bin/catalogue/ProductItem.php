<?php
class ProductItem{
    const IMG_PREFIX = "item";
    const SIZE_SMALL = "small";
    const SIZE_MEDIUM = "medium";
    const SIZE_LARGE = "large";
    const SIZE_HUGE = "huge";

    public $id;             // int primary key
    public $label;          // string
    public $title;          // string
    public $children_id;    // "1,2,3,4,5"
    public $description;    // string
    public $content;        // string
    public $url;            // string
    public $type;           // int 1,2,3
    public $amount;         // int
    public $brand_id;       // int
    public $hidden;         // bool

    private $image_data;    // string
    private $price;         // float
    private $currency_id;   // int

    private $props;

    public function __construct($data){
        $this->id = intval($data["id"]);
        if (preg_match("/[\=?\&?]/", $data["image"])){
            parse_str($data["image"], $this->image_data);
        }else{
            // DEBUG
            $this->image_data["small"] = $data["image"];
            $this->image_data["medium"] = $data["image"];
            $this->image_data["large"] = $data["image"];
            //
        }
        $this->type = intval($data["type"]);
        $this->label = (string) $data["label"];
        $this->title = (string) $data["title"];
        $this->children_id = (string) $data["children_id"];
        $this->url = $data["url"];
        $this->description = $data["description"];
        $this->content = $data["content"];
        $this->amount = intval($data["amount"]);
        $this->brand_id = $data["brand_id"];
        $this->hidden = ($data["hidden"] == "true" || $data["hidden"] == "1" ? true : false);

        $this->price = floatval($data["price"]);
        $this->currency_id = (intval($data["currency_id"]) > 0 ? intval($data["currency_id"]) : 1);

        $this->price = floatval($data["price"]);

        $this->props = $this->unserializeProps($data["props"]);
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------
    private function unserializeProps($data){
        $properties = array();
        if ($data == ""){
            return $properties;
        }
        parse_str($data, $data);
        foreach($data as $key=>$value){
            $properties[] = G::$pageData->propsManager->createProperty($key, $value);
            G::logMessage("props: $key = $value");
        }
        return $properties;
    }
// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function getPrice($request_mark){
        if ($this->price == 0.0){
            return 0.0;
        }
        $converter = G::$pageData->currencyConverter;
        $result = $converter->convert($this->price, $this->currency_id, $request_mark);
        return round($result, 2);
    }

    public function getImageUrl($size){
        $img_url = PATH_CAT_IMAGES;
        switch($size){
            case self::SIZE_SMALL:
                $img_url .= "small/";
                $img_url .= $this->image_data["small"];
                break;
            case self::SIZE_MEDIUM:
                $img_url .= "medium/";
                $img_url .= $this->image_data["medium"];
                break;
            case self::SIZE_LARGE:
                $img_url .= "large/";
                $img_url .= $this->image_data["large"];
                break;
            case self::SIZE_HUGE:
                $img_url .= "huge/";
                $img_url .= $this->image_data["huge"];
                break;

        }
        $img_url .= ".jpg";

        return $img_url;
    }

    public function getImagesData(){
        return $this->image_data;
    }

    public function getProp($name){
        return $this->props[$name];
    }

    public function getProps(){
        return $this->props;
    }

    public function __toString(){
        return "ProductItem[" . $this->id . "] " . $this->title;
    }
}
?>