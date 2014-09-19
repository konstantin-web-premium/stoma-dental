<?php
$text_example = G::$language->getText("common", "for_example_text");
// TODO example
$text_search = G::$language->getText("common", "search_text");
$tels = G::$pageData->getSetting("tel");

$tel_rendered = array();
foreach($tels as $tel){
    $call_tel = preg_replace("/[\(\)\- ]*/", "", $tel);
    $short_tel = preg_replace("/(^\+38)+/", "", $tel);
    $tel_rendered[] = "<a href=\"tel:$call_tel\">$short_tel</a>";
}

?>
<div class="header-search-container" style="float:right;">
    <?php
        echo implode(", ", $tel_rendered);
    ?>
    <!--
    <form id="search_form" action="/search">
        <input type="text" name="search"/>
        <button type="submit">&nbsp;</button>
    </form>
    -->
</div>