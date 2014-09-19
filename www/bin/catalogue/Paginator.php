<?php
class Paginator{
    const PAGE_ITEMS_LIMIT = "15";
    const PAGES_ROW_LIMIT = "5";

    public $page;
    public $max_pages;

    public function __construct($max_pages, $page){
        $this->max_pages = $max_pages;
        $this->page = ($page < 1
                            ? 1
                            : ($page > $max_pages
                                ? $max_pages
                                : $page));
    }

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------


// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------

    public function render(){
        $url_prev = ($this->page > 1 ? "<a href='?page=" . ($this->page - 1) . "'>&#8592;</a> " : "<span class='nohref'>&#8592;</span> ");
        $url_next = ($this->page < $this->max_pages ? "<a href='?page=" . ($this->page + 1) . "'>&#8594;</a> " : "<span class='nohref'>&#8594;</span> ");

        // start
        $view = "<div class='catalogue-paginator'>" . $url_prev;

        // set default " < 1 2 3 > "
        $from = 1;
        $to = $this->max_pages;

        // if pages amount more than can show " < 1 ... 3 5 6 ... 10 > "
        // any way FIRST and LAST pages are shown
        if ($this->max_pages > self::PAGES_ROW_LIMIT){
            // if page is on FIRST half amount of total pages -> set $from FIRST
            if ($this->page < $this->max_pages / 2){
                $from = $this->page - floor(self::PAGES_ROW_LIMIT / 2);
                if ($from < 2){
                    $from = 2;
                }
                $to = $from + self::PAGES_ROW_LIMIT;
                if ($to > $this->max_pages-1){
                    $to = $this->max_pages-1;
                }
            }
            // if page is on SECOND half amount of total pages -> set $to FIRST
            else
            {
                $to = $this->page + floor(self::PAGES_ROW_LIMIT / 2);
                if ($to > $this->max_pages-1){
                    $to = $this->max_pages-1;
                }
                $from = $to - self::PAGES_ROW_LIMIT;
                if ($from < 1){
                    $from = 1;
                }
            }

            // insert space for width if triple-dots is hidden
            if($from <= 2){
                $view .= "<span class='missed'>&nbsp;</span>";
            }
            // insert FIRST page any way
            $view .= ($this->page == 1 ? "<span class='selected'>1</span> " : "<a href='?page=1'>1</a> ");
            // triple-dots
            if($from > 2){
                $view .= "<span class='missed'>&#133;</span>";
            }
        }else{
            // insert space for common style width
            $view .= "<span class='missed'>&nbsp;</span>";
        }


        // insert pages
        for($i = $from; $i <= $to; $i++){
            if ($i == $this->page){
                $view .= "<span class='selected'>$i</span> ";
            }else{
                $view .= "<a href='?page=$i'>$i</a> ";
            }
        }

        // if pages amount more than can show
        if ($this->max_pages > self::PAGES_ROW_LIMIT){
            // triple-dots
            if($to < $this->max_pages-1){
                $view .= "<span class='missed'>&#133;</span>";
            }
            // insert LAST page any way
            $view .= ($this->page == $this->max_pages ? "<span class='selected'>$this->max_pages</span>" : "<a href='?page=$this->max_pages'>$this->max_pages</a> ");
            // insert space for width if triple-dots is hidden
            if($to >= $this->max_pages-1){
                $view .= "<span class='missed'>&nbsp;</span>";
            }
        }else{
            // insert space for common style width
            $view .= "<span class='missed'>&nbsp;</span>";
        }

        // finalize
        $view .= $url_next . "</div>";

        return $view;
    }
}
?>