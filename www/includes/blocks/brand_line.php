<?php
//
$brands = G::$pageData->getBrands();
$brands_text = G::$language->getText("common", "brands_text");
$all_brands_text = G::$language->getText("common", "all_brands");
?>

<h1 style="margin-bottom: 0;"><?php echo $brands_text;?></h1>
<div class="content-white-back-brands">
    <ul class="block-list">
        <?php

        if (count($brands)){
            foreach($brands as $brand){
                if (!$brand->order || $brand->hidden){
                    continue;
                }

                $logo = $brand->getLogoUrl("medium");
                if (strlen($logo)){
                    $img = "<img src='$logo' height='50' style='margin-right: 20px;' />";
                }else{

                    $img = $brand->name;
                }

                ?>
                <li>
                    <?php

                    echo "<a " . (!empty($brand->description) ? "href='/brands/$brand->label'" : "nohref") . ">$img</a>";

                    ?>
                </li>
                <?php

            }
        }
        ?>

</div>

