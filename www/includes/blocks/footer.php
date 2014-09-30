<?php
$footer_menu      = G::$pageData->getMenu("footer_menu");
$tel              = G::$pageData->getSetting("tel");
$text_stayWithUs  = G::$language->getText("common", "stay_with_us");
$text_company     = G::$language->getText("common","company_name_full");
$text_schedule    = G::$language->getText("common", "schedule");
?>

<ul class="block-list">
    <li>
        <div class="text-block-first">
            <?php

                echo $text_schedule .
                    "<p></p>" .
                    implode("<br />", $tel);
            ?>
        </div>
    </li><li>
        <div class="footer-menu">
            <?php
            if ($footer_menu){
                foreach($footer_menu as $menu){
                    $title = "\" title=\"".(isset($menu["title"]) ? $menu["title"] : "");
                    ?>

                    <a href="<?php echo $menu["url"].$title; ?>"><?php echo $menu["content"]; ?></a>

                <?php
                }
            }
            ?>
        </div>
    </li><li>
        <div class="footer-menu">
            <?php
			
			$pageData_socials = new PageData();
            $socials = $pageData_socials->getSocials();
			            
            $socials = G::$pageData->getSocials();
            if (is_array($socials) && count($socials) > 0){
                echo $text_stayWithUs . ":<br />";
                foreach($socials as $item){
                    $url = $item["url"];
                    $alt = $item["alt"];
                    $icon = $item["icon"];

                    ?>
                    <a href="<?php echo $url; ?>" title="<?php echo $alt; ?>" target="_blank"><img alt="<?php echo $alt; ?>" src="<?php echo "/images/icons/$icon"; ?>" /></a><br />
                <?php

                }
			}
            ?>
            
        </div>
    </li><li>
        <div class="text-block">
        </div>
    </li>
</ul>
