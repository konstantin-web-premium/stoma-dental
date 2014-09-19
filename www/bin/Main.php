<?php
// secure access through index only
if (!defined("A")){
    die();
}

session_start();

include_once "config.php";
include_once "G.php";       // Global
include_once "Language.php";
include_once "UserData.php";
include_once "PageData.php";

// -------------------------------------------------------------------------------------------------
// IMPLEMENTATION ----------------------------------------------------------------------------------
// -------------------------------------------------------------------------------------------------

// DB connection ------------------------------------------------------------------

//$connection = new Connection(SERVER, USERNAME, PASSWORD, DB_NAME);
G::connect(SERVER, USERNAME, PASSWORD, DB_NAME);

//init page data ------------------------------------------------------------------
G::$pageData = new PageData($_GET["_page"]);

// User data (auth) ---------------------------------------------------------------
G::$user = new UserData();
G::$user->loadFromCookies();
// localization -------------------------------------------------------------------
G::$language = new Language();
G::$language->setLanguage((G::$user->languageId ? G::$user->languageId : G::$pageData->getSetting("default_language_id")));

// plug in ADMIN MODULES ----------------------------------------------------------
if (G::$user->isOrHigher(U_MODERATOR)){
    include_once(ROOT . PATH_MODULES . "config.php");
}

// init page render ---------------------------------------------------------------
G::initPageRender();

G::addToRender("<div class=\"overtop-block\">", BLOCK_OVERTOP);


// load page data -----------------------------------------------------------------
G::$pageData->load();

// ------- HEAD --------------------
//

// ------ OVERTOP ------------------
G::addToRender("</div>", BLOCK_OVERTOP);
//

// ------ HEADER ------------------
G::addToRender("blocks/header_top_menu.php", BLOCK_TOP_MENU, CODE);
G::addToRender("blocks/header_search_block.php", BLOCK_SEARCH, CODE);
G::addToRender("blocks/header_middle_menu.php", BLOCK_MIDDLE_MENU, CODE);
#G::addToRender("blocks/header_auth_and_cart.php", BLOCK_AUTH, CODE);
G::addToRender("blocks/header_s_cart.php", BLOCK_SCART, CODE);
G::addToRender("blocks/carousel.php", BLOCK_CAROUSEL, CODE);

// ------ FOOTER -----------------
G::addToRender("blocks/footer.php", BLOCK_FOOTER, CODE);
#addToRender("external/pluso.php", BLOCK_FOOTER, CODE);
//

?>