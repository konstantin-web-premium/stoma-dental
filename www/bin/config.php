<?php
//DB connection data
    /** user - DEV_LOCAL    rabotaeu_sdbeta - BETA    ? - production
     *  pass                gIX4D1UcY
     */
// ALPHA
define("SERVER", "localhost");
define("USERNAME", "user");
define("PASSWORD", "pass");
define("DB_NAME", "sd_dev");
// Strings
define("ROOT", $_SERVER["DOCUMENT_ROOT"]);
define("DATABASE_ERROR_MESSAGE", "Database ERROR");
define("DATABASE_ERROR_RENDER", "<p style='background-color:#f99; text-align:center;'>Database ERROR</p>");
define("LOCALIZ_PREFIX", "#_");#localization from DB PREFIX
define("CATALOGUE_ROOT_LABEL", "catalogue");
define("WEBSITE_VERSION", "0.2.4");

// FILES
define("CSS_FILE", "styles.css");
define("CSS_ADMIN_FILE", "styles_admin.css");
define("META_FILE", "head/meta.php");
define("ADMIN_META_FILE", "head/meta_admin.php");
##define("SERVICE_AJAX_FILE", "service.ajax.php");


//PATHs
define("PATH_BIN", "/bin/");
define("PATH_INCLUDES", "/includes/");
define("PATH_MODULES", "/bin/modules/");
define("PATH_SCRIPTS", "/scripts/");
define("PATH_STYLES", "/styles/");
define("PATH_SERVICES", "/services/");
define("PATH_EXTERNAL", "/external/");
define("PATH_IMAGES", "/images/");
define("PATH_CAT_IMAGES", "/images/catalogue/");
define("PATH_BRANDS_IMAGES", "/images/brands/");
define("PATH_NEWS_IMAGES", "/images/news/");

//PAGE Categories
define("P_TYPE_CATALOGUE", "catalogue");
define("P_TYPE_ARTICLE", "article");
define("P_TYPE_NEWS", "news");
define("P_TYPE_BRAND", "brands");
define("P_TYPE_ADMIN", "admin");
define("P_TYPE_PAGE", "page");
define("P_TYPE_TECH", "technical");

// NEWS TYPES
define("NEWS_TYPE_NEWS", "1");
define("NEWS_TYPE_PROMO", "2");

// PROPERTIES types
define("PROP_TYPE_SWITCH", "1");
define("PROP_TYPE_SWITCH_STRICT", "2");
define("PROP_TYPE_COLOR", "3");
define("PROP_TYPE_EXIST", "4");
define("PROP_TYPE_RANGE", "5");

// PRODUCTS TYPES
define("TYPE_CATEGORY", "1");
define("TYPE_PRODUCT_BIG", "2");
define("TYPE_PRODUCT_SMALL", "3");

// TECHNICAL PAGES
define("PAGE_INDEX", "index");
define("PAGE_LOGOUT", "logout");
define("PAGE_LOGIN", "login");
define("PAGE_ENTER", "enter");
define("PAGE_REGISTER", "register");
define("PAGE_ACCESS_DENIED", "access_denied");
define("PAGE_PAGE404", "404");

// RENDER Blocks
#       name      label
define("BLOCK_OVERTOP", "overtop");
define("BLOCK_HEAD", "head");
define("BLOCK_PROMO", "promo");
define("BLOCK_TOP_MENU", "top_menu");
define("BLOCK_SEARCH", "search");
define("BLOCK_AUTH", "auth");
define("BLOCK_SCART", "s_cart");
define("BLOCK_MIDDLE_MENU", "middle_menu");
define("BLOCK_PRE_CONTENT", "pre_content");
define("BLOCK_CONTENT", "content");
define("BLOCK_SIDEBAR_LEFT", "sidebar_left");
define("BLOCK_SIDEBAR_RIGHT", "sidebar_right");
define("BLOCK_FOOTER", "footer");
define("BLOCK_CAROUSEL", "carousel");
define("BLOCK_NEWS", "news");
define("BLOCK_RECENT", "recent");
define("BLOCK_COUNTERS", "counters");

//RENDER DATA TYPES
define("TEXT", "text_data_type");
define("FILE", "file_data_type");
define("CODE", "code_data_type");

// USERS SENIORITY
define("U_ADMIN", "1");
define("U_MODERATOR", "2");
define("U_AUTH", "3");
define("U_GUEST", "4");
define("U_GUEST_BLIND", "5");

// LOCALIZATION
define("ERRORS", "errors");

// Tables -------------------------------------------------------------------------------
define("TABLE_USERS","users");
define("TABLE_LOCALIZATION", "localization");
define("TABLE_LANGUAGE", "language");
define("TABLE_SETTINGS", "site_settings");
define("TABLE_PAGES", "pages");
define("TABLE_META", "meta_pages");
define("TABLE_MENU", "menu_list");
define("TABLE_MENU_ITEMS", "menu_items");
define("TABLE_CATALOGUE", "catalogue");
define("TABLE_CATALOGUE_PROPS", "catalogue_props");
define("TABLE_PAGEBLOCKS", "page_blocks");
define("TABLE_SOCIALS", "socials");
define("TABLE_BRANDS", "brands");
define("TABLE_COUNTRIES", "countries");
define("TABLE_NEWS", "news");
define("TABLE_CURRENCY", "currency");

//SETTINGS (table = site_settings)
define("SET_TITLE", "title");
define("SET_HOST", "host");
define("SET_AUTHOR", "author");
define("SET_COPY", "copyright");
define("SET_AUTHOR_URL", "author_url");
define("SET_PUBLISHER_URL", "publisher_url");
define("SET_ICON", "icon");
define("SET_SH_ICON", "shortcut icon");

// LIMITS
# page
define("PAGE_LABEL_REG_EXP", "/^[a-z0-9\-\/_]+$/");
define("LABEL_REG_EXP", "/^[a-z0-9\-_]+$/");
#username
//define ("LOGIN_REG_EXP", "/^[a-zA-Z0-9]+$/");
define ("LOGIN_REG_EXP", "/.+@.+\..+/"); // email
define ("NAME_MIN_LENGTH", "2");
#password
define ("PASSWORD_REG_EXP", "");
define ("PASSWORD_MIN_LENGTH", "3");
define ("PASSWORD_MAX_LENGTH", "32");
#cookie
define("COOKIE_EXPIRE_TIME", "2592000"); //60*60*24*30
?>