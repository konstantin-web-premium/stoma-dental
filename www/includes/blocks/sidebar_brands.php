<?php
//
$brands = G::$pageData->getBrands();
$brands_text = G::$language->getText("common", "brands_text");
$all_brands_text = G::$language->getText("common", "all_brands");
?>

<h1><?php echo $brands_text;?></h1>
<div class="content-white-back-sidebar-brand">
<div class="brands-list-table">
    
    <?php

    $total = 0;
    if (count($brands)){
        foreach($brands as $brand){
            if (!$brand->order || $brand->hidden){
                continue;
            }

            if ($total % 2 == 0){
                
            }

            $logo = $brand->getLogoUrl("medium");
            if (strlen($logo)){
                $img = "<img src='$logo' />";
            }else{

                $img = $brand->name;
            }

            ?>
            
                <!--a href="/brands/<?php echo $brand->label;?>"-->
                    <?php echo $img; ?>
                <!--/a-->
            
            <?php
            $total++;
        }
    }
    ?>
    <div style="clear:both;">&nbsp;</div>
    
</div>
</div>

