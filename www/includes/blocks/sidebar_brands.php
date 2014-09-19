<?php
//
$brands = G::$pageData->getBrands();
$brands_text = G::$language->getText("common", "brands_text");
$all_brands_text = G::$language->getText("common", "all_brands");
?>

<h1><?php echo $brands_text;?></h1>
<div class="content-white-back">
<table class="brands-list-table">
    <tr>
    <?php

    $total = 0;
    if (count($brands)){
        foreach($brands as $brand){
            if (!$brand->order || $brand->hidden){
                continue;
            }

            if ($total % 2 == 0){
                echo "</tr><tr>";
            }

            $logo = $brand->getLogoUrl("small");
            if (strlen($logo)){
                $img = "<img src='$logo' />";
            }else{

                $img = $brand->name;
            }

            ?>
            <td>
                <a href="/brands/<?php echo $brand->label;?>">
                    <?php echo $img; ?>
                </a>
            </td>
            <?php
            $total++;
        }
    }
    ?>
    <div style="clear:both;">&nbsp;</div>
    </tr>
</table>
</div>

