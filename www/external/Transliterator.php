<?php
/**
 * @author bulforce[]gmail.com # 2011
 * Simple class to attempt transliteration of bulgarian lating text into bulgarian cyrilic text
 */

// Usage:
// $text = "yagoda i yundola";
// $tl = new Transliterate();
// echo $tl->lat_to_cyr($text); //ягода и юндола

class Transliterate {

    private $cyr_identical = array("а", "б", "в", "в", "г", "д", "е", "ж", "з", "и", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ъ", "я");
    private $lat_identical = array("a", "b", "v", "w", "g", "d", "e", "j", "z", "i", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "y", "q");
    private $cyr_fricative = array("ж", "ч", "ш", "щ", "ц", "я", "ю", "я", "ю");
    private $lat_fricative = array("zh", "ch", "sh", "sht", "ts", "ia", "iu", "ya", "yu");

    public function __construct() {
        $this->identical_to_upper();
        $this->fricative_to_variants();
    }

    public function lat_to_cyr($str) {

        for ($i = 0; $i < count($this->cyr_fricative); $i++) {
            $c_cyr = $this->cyr_fricative[$i];
            $c_lat = $this->lat_fricative[$i];
            $str = str_replace($c_lat, $c_cyr, $str);
        }

        for ($i = 0; $i < count($this->cyr_identical); $i++) {
            $c_cyr = $this->cyr_identical[$i];
            $c_lat = $this->lat_identical[$i];
            $str = str_replace($c_lat, $c_cyr, $str);
        }

        return $str;
    }

    private function identical_to_upper() {

        foreach ($this->cyr_identical as $k => $v) {
            $this->cyr_identical[] = mb_strtoupper($v, 'UTF-8');
        }

        foreach ($this->lat_identical as $k => $v) {
            $this->lat_identical[] = mb_strtoupper($v, 'UTF-8');
        }
    }

    private function fricative_to_variants() {
        foreach ($this->lat_fricative as $k => $v) {
            // This handles all chars to Upper
            $this->lat_fricative[] = mb_strtoupper($v, 'UTF-8');
            $this->cyr_fricative[] = mb_strtoupper($this->cyr_fricative[$k], 'UTF-8');

            // This handles variants
            // TODO: fix the 3 leter sounds
            for ($i = 0; $i <= count($v); $i++) {
                $v[$i] = mb_strtoupper($v[$i], 'UTF-8');
                $this->lat_fricative[] = $v;
                if ($i == 0) {
                    $this->cyr_fricative[] = mb_strtoupper($this->cyr_fricative[$k], 'UTF-8');
                } else {
                    $this->cyr_fricative[] = $this->cyr_fricative[$k];
                }
                $v[$i] = mb_strtolower($v[$i], 'UTF-8');
            }
        }
    }

}
?>