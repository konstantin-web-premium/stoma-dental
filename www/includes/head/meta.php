<?php

?>
<title><?php echo G::$language->getText("common", "title"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo G::$language->charset; ?>" />
<meta name="viewport" content="width=device-width" /> 
<!-- <meta http-equiv="content-language" content="<?php echo G::$language->code; ?>" /> -->
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> -->
 
<!-- <meta name="title" content="<?php echo G::$pageData->title; ?>" /> -->
<meta name="robots" content="<?php echo G::$pageData->data["robots"]; ?>" />
<meta name="description" content="<?php echo G::$pageData->data["description"]; ?>" />
<meta name="keywords" content="<?php echo G::$pageData->data["keywords"]; ?>" />
<?php

if (G::$pageData->data["author"]){
    echo "<meta name=\"author\" content=\"" . G::$pageData->data["author"] . "\" />\n";
}
if (G::$pageData->data["copyright"]){
    echo "<meta name=\"copyright\" content=\"" . G::$pageData->data["copyright"] . "\" />\n";
}


if (false/*SPIKE to hide*/ && isset(G::$pageData->data["og"])){
?>
<meta property="og:title" content="<?php echo G::$pageData->data["og"]["title"] ?>" />
<meta property="og:url" content="<?php echo G::$pageData->data["og"]["url"] ?>" />
<meta property="og:image" content="<?php echo G::$pageData->data["og"]["image"] ?>" />
<meta property="og:description" content="<?php echo G::$pageData->data["og"]["description"] ?>" />
<meta property="og:site_name" content="<?php echo G::$pageData->getSetting(SET_TITLE); ?>" />
<meta property="og:type" content="<?php echo G::$pageData->data["og"]["type"] ?>" />
<meta property="og:locale" content="<?php echo G::$language->og_code ?>" />
<?php
}
?>

<?php
/* TWITTER
"<meta name="twitter:card" content="summary" /> 
"<meta name="twitter:site" content="Имя Twitter аккаунта" /> 
"<meta name="twitter:title" content="Заголовок страницы для Twitter" /> 
"<meta name="twitter:description" content="Описание страницы для Twitter" /> 
"<meta name="twitter:url" content="http://www gggg com" />
" 
*/

if (isset(G::$pageData->getSetting[SET_AUTHOR_URL])){
?>
    <link rel="author" href="<?php echo G::$pageData->getSetting[SET_AUTHOR_URL]; ?>" />
<?php
}

if (isset(G::$pageData->getSetting[SET_PUBLISHER_URL])){
?>
    <link rel="publisher" href="<?php echo G::$pageData->getSetting[SET_PUBLISHER_URL]; ?>" />
<?php
}

if (isset(G::$pageData->data["canonical"])){
?>
    <link rel="canonical" href="<?php echo G::$pageData->data["canonical"] ?>" />
<?php
}

if (isset(G::$pageData->getSetting[SET_ICON])){
?>
    <link rel="icon" type="image/png" href="<?php echo G::$pageData->getSetting[SET_ICON]; ?>" />
<?php
}

if (isset(G::$pageData->getSetting[SET_SH_ICON])){
?>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo G::$pageData->getSetting[SET_SH_ICON]; ?>" />
<?php
}
?>
