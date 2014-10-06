<?php
//$brands = G::$pageData->getBrands();
$news = G::$pageData->getNewsList();
//include config for gallery
include_once $_SERVER["DOCUMENT_ROOT"] . "/scripts/config_gallery.php";
?>
<div class="gallery-wrapper">
    <div class="wrapper-inner-container">
        <div class="gallery">
            <div class="gallery1">
                <?php

			    foreach($img as $key => $value){
                    echo '<a href="'. $value .'"><img src="' . $_SERVER["DOCUMENT_ROOT"] . $key . '"/>';
			    }

                    ?>
                    </div>

                    <div class="gallery-scroll-box">
                    <?php

                    $i = 0;
                    while(($item = array_shift($news)) && ($i <= 3)){
                        if ($item["type"] != NEWS_TYPE_NEWS){
                            continue;
                        }

                        $i++;
                        $img_url = PATH_NEWS_IMAGES . $item["image"] . ".jpg";

                        ?>
                        <div class="news-item">
                            <a href="/news/<?php echo $item["label"]?>">
                                <img src="<?php echo $img_url; ?>" alt="<?php echo $item["short"]; ?>" />
                            </a>
                            <div class="text-block">
                                <a href="/news/<?php echo $item["label"]?>">
                                    <?php echo $item["short"];?>
                                </a>
                            </div>
                        </div>
                    <?php

                    }
                }

                /*
                foreach($brands as $brand){
                    echo "<tr><td>" .
                    "<a href=\"#\">$brand->name</a></td><td><img src=\"" . $brand->getLogoUrl("small") . "\" class=\"logo\" alt=\"$brand->name\" />" .
                    "</td></tr>\n";
                }
                */

                ?>
            </div>
        </div> <!-- gallery -->
    </div> <!-- wrapper-inner-container -->
</div> <!-- gallery-wrapper -->
