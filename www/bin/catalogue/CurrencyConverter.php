<?php
class CurrencyConverter{

    private $currency;

    public function __construct($currency){
        $this->currency = $currency;
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function getCurrencyByMark($mark){
        foreach($this->currency as $curr){
            if ($curr["mark"] == $mark){
                return $curr;
            }
        }
        return array();
    }

    public function getCurrencyById($id){
        return $this->currency[$id];
    }

    /**
     * @param $value (float)
     * @param $from - (int) id || (string) mark
     * @param $to - (int) id || (string) mark
     * @return float
     */
    public function convert($value, $from, $to){
        if($from === $to){
            return $value;
        }
        if (is_int($from)){
            $currency_from  = $this->getCurrencyById($from);
        }else{
            $currency_from  = $this->getCurrencyByMark($from);
        }
        if (is_int($to)){
            $currency_to  = $this->getCurrencyById($to);
        }else{
            $currency_to  = $this->getCurrencyByMark($to);
        }

        if($currency_from["id"] == $currency_to["id"]){
            $result = $value;
        }else{
            $result = $value * $currency_from["rate"] / $currency_to["rate"];
        }
        return $result;
    }

}
?>