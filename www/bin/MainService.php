<?php
include_once "config.php";
include_once "G.php";
include_once "Language.php";
include_once "UserData.php";
include_once "PageData.php";
include_once "PopupManager.php";


define("REG_SUBMIT", "regSubmit");
define("LOAD_PAGE_DATA", "loadPageData");
define("ITEM_EDIT_SUBMIT", "itemEditSubmit");
define("PAGE_DELETE", "pageDelete");
define("GET_CATEGORIES_TREE", "getCategoriesTree");
define("GET_POPUP_CONTENT", "getPopupWindowContent");
define("UPLOAD_IMAGE", "uploadImage");
define("CROP_PRODUCT_IMAGE", "cropProductImage");
define("COPY_IMAGE_TMP", "copyImageTmp");
define("DELETE_IMAGE", "deleteImage");

function init($label){
    // PageData init
    G::$pageData = new PageData($label);
    // User data (auth) ---------------------------------------------------------------
    G::$user = new UserData();
    G::$user->loadFromCookies();
    // localization -------------------------------------------------------------------
    G::$language = new Language();
    G::$language->setLanguage((G::$user->languageId ? G::$user->languageId : G::$pageData->getSetting("default_language_id")));
    // admin modules
    if (G::$user->isOrHigher(U_MODERATOR)){
        include_once($_SERVER["DOCUMENT_ROOT"] . PATH_MODULES . "config.php");
    }
}

session_start();

G::$dataType = G::DATATYPE_AJAX;
G::$ajaxData = array(
    "action" => $_POST["action"],
    "actionId" => $_POST["actionId"]
);
$html = ($_POST["html"] == "true");
G::$dataType = $html ? G::DATATYPE_AJAX_HTML : G::DATATYPE_AJAX;

$data = array();
// DB connection ------------------------------------------------------------------
G::connect(SERVER, USERNAME, PASSWORD, DB_NAME, $html);

switch (G::$ajaxData["action"]){
    default:
        break;
    case REG_SUBMIT:
        $f_nonbotable = $_POST["nonbotable"];
        $f_email      = $_POST["email"];
        $f_keystring  = $_POST["keystring"];
        $bot_suspect  = $_SESSION["bot_suspect"];

        $serialized_input = (strlen($f_email) > 0 ? "1" : "0") .
                            (isset($f_nonbotable) ? "1" : "0") .
                            ($bot_suspect         ? "1" : "0") .
                            (strlen($f_keystring) > 0 ? "1" : "0");

        $allow_register = false;
        $ignore_captcha = false;
        $show_captcha = false;

        switch($serialized_input){
            default:
                // reg with captcha
                $_SESSION["bot_suspect"] = true;
                $allow_register = true;
                $ignore_captcha = false;
                $show_captcha = true;
                break;
            case "0000":
                // false -> show captcha
                $_SESSION["bot_suspect"] = true;
                $show_captcha = true;
                break;
            case "0100":
            case "0101":
                # HUMAN over 60%
                $allow_register = true;
                $ignore_captcha = true;
                $show_captcha = false;
            break;
            case "1010":
            case "1011":
                # BOT over 75%
                # may be block ID in further
                # NO ACTION
                break;
        }

        //unset($_SESSION["bot_suspect"]);

        $reg_result = false;

        if ($allow_register){
            // User data
            G::$user = new UserData();
            // localization
            G::$language = new Language();

            # TODO default lang ???
            G::$language->setLanguage((G::$user->languageId ? G::$user->languageId : 2));

            $reg_result = G::$user->registerUser($_POST, $ignore_captcha);
        }

        $data = array();

        $data["result"] = $reg_result;
        $data["show_captcha"] = $show_captcha;
        $data["errors"] = G::$user->getErrors(true);
        $data["errors_key"] = G::$user->getErrorsKey();
        $data["debug"] = $serialized_input;

        break;

    case LOAD_PAGE_DATA:
        switch($_POST["edit_type"]){
            case "page":
            default:
                $label = $_POST["label"];
                break;
            case "catalogue":
                $id = intval($_POST["id"]);
                $label = ($id == 1 ? "" : "catalogue/") . PageUtils::getLabelById($id, TABLE_CATALOGUE);
                if (!$label){
                    G::fatalError("Page with id=$id not found");
                }
                break;
        }
        init($label);
        // PageData load
        G::$pageData->load();
        // get response data
        $data = G::$pageData->data;
        $data["address"] = G::$pageData->getAddress();
        // SPIKE -> get first parent
        if(G::$pageData->getAddress("type") == P_TYPE_CATALOGUE){
            // data for admin edit
            $product = G::$pageData->data["current_product"];
            if ($product){
                $data["price_uah"] = $product->getPrice("UAH");
                $data["price_usd"] = $product->getPrice("USD");
                $data["content"] = $product->content;
                $images_data = $product->getImagesData();
                $data["img_small"] = $images_data["small"];
                $data["img_medium"] = $images_data["medium"];
                $data["img_large"] = $images_data["large"];
                $brand = G::$pageData->getBrand($products->brand_id);
                if ($brand){
                    $data["brand_id"] = $brand->id;
                    $data["brand_data"] = "$brand->name ($brand->country)";
                    $data["brand_img_url"] = $brand->getLogoUrl("small");
                }
            }
            //
            $parents = CatalogueUtils::findParentsOf($data["id"]);
            $data["parent_id"] = $parents[0]["id"];
        }
        unset($data["current_categories"]);
        unset($data["catalogue"]);
        unset($data["products"]);
        unset($data["current_product"]);

        $data["result"] = isset(G::$pageData->data);
        G::logMessage("loaded as : " . print_r($data, true));
        //
        break;
    case ITEM_EDIT_SUBMIT:
            switch($_POST["edit_type"]){
                default:
                case "page":
                    init("admin/");
                    if (G::$user->isOrHigher(U_MODERATOR)){
                        $result = G::$pageData->update($_POST);
                        $data = G::$pageData->data;
                        $data["result"] = $result;
                        $data["errors"] = G::$pageData->getErrors();

                    $pages = PageUtils::getItemsList("pages");

                        $data["itemList"] = $pages;
                    }else{
                        $data["result"] = false;
                        $data["errors"] = array("access denied");
                    }
                    break;
                case "catalogue":
                    init("admin/");
                    if (G::$user->isOrHigher(U_MODERATOR)){
                        $result = G::$pageData->updateCatalogue($_POST);
                        if ($result){
                            $p_data = G::$pageData->data;
                            $p_data["tr_data"] = RenderUtils::renderNodeTableRow($p_data);
                            // data for admin edit
                            $product = G::$pageData->data["current_product"];
                            //DEBUG
                            G::logMessage("product => $product");
                            //
                            if ($product){
                                $p_data["price_uah"] = $product->getPrice("UAH");
                                $p_data["price_usd"] = $product->getPrice("USD");
                                $p_data["content"] = $product->content;
                                $images_data = $product->getImagesData();
                                $p_data["img_small"] = $images_data["small"];
                                $p_data["img_medium"] = $images_data["medium"];
                                $p_data["img_large"] = $images_data["large"];
                                $brand = G::$pageData->getBrand($product->brand_id);
                                if ($brand){
                                    $p_data["brand_name"] = $brand->name;
                                    $p_data["brand_data"] = "$brand->name ($brand->country)";
                                    $p_data["brand_img_url"] = $brand->getLogoUrl("small");
                                }
                            }
                            //
                            unset($p_data["current_categories"]);
                            unset($p_data["catalogue"]);
                            unset($p_data["products"]);
                            unset($p_data["current_product"]);
                            $parents = CatalogueUtils::findParentsOf($p_data["id"]);

                            $p_data["parent_id"] = $parents[0]["id"];
                        }
                        $data = $p_data;
                        $data["result"] = $result;
                        $data["errors"] = G::$pageData->getErrors();

                        //$pages = PageUtils::getItemsList("catalogue");

                        //$data["itemList"] = $pages;
                    }else{
                        $data["result"] = false;
                        $data["errors"] = array("access denied");
                    }
                    break;
            }
        break;
    case PAGE_DELETE:
        init($_POST["label"]);

        if (G::$user->isOrHigher(U_MODERATOR)){
            $result = G::$pageData->delete();
            $data["result"] = $result;
            $data["errors"] = G::$pageData->getErrors();

            $fields = array(
                TABLE_PAGES . ".id",
                "title",
                "label");
            $pages = PageUtils::getItemsList($fields);

            $data["pageList"] = $pages;
        }else{
            $data["result"] = false;
            $data["errors"] = array("access denied");
        }
        break;
    case GET_CATEGORIES_TREE:
        init("admin/catalogue");

        // PageData load
        G::$pageData->load();

        if (G::$user->isOrHigher(U_MODERATOR)){
            include $_SERVER["DOCUMENT_ROOT"] . PATH_INCLUDES . "admin/admin_categories_tree.php";
        }else{
            echo "ACCESS DENIED";
        }
        die();
        break;
    case GET_POPUP_CONTENT:
        $label = $_POST["label"];
        if (!preg_match("/^\/?admin\/.*/", $label)){
            G::fatalError("Access denied!");
        }
        init($label);
        // PageData load
        G::$pageData->load();

        $popup = new PopupManager($_POST["type"]);
        $popup->renderPopupContent();
        die();
        break;
    case UPLOAD_IMAGE:
        init("admin/catalogue/product");
        if (G::$user->isOrHigher(U_MODERATOR)){
            $data = AdminUtils::uploadProductImage($_FILES["file_browse"]);
            G::logMessage($data["img_filename"]);
            $data["result"] = true;
        }else{
            $data = array();
            $data["result"] = false;
            $data["errors"] = array(RenderUtils::renderError("Access denied!"));
        }
        break;
    case CROP_PRODUCT_IMAGE:
        init("admin/catalogue/product");
        if (G::$user->isOrHigher(U_MODERATOR)){
            $filename   = $_POST["filename"];
            $width      = intval($_POST["width"]);
            $height     = intval($_POST["height"]);
            $x          = intval($_POST["x1"]);
            $y          = intval($_POST["y1"]);
            G::logMessage("CROP with $width x $height ($x:$y) as $_POST[image_type]");
            switch($_POST["image_type"]){
                case "c_small":
                    $new_width = 40;
                    $new_height = 40;
                    break;
                case "c_medium":
                default:
                    $new_width = 270;
                    $new_height = 180;
                    break;
                case "c_large":
                    $new_width = 0;
                    $new_height = 0;
                    break;
            }
            G::logMessage("CROP :: " . $filename);
            $data = AdminUtils::cropProductImage($filename, $width, $height, $x, $y, $new_width, $new_height);
        }else{
            $data = array();
            $data["result"] = false;
            $data["errors"] = array(RenderUtils::renderError("Access denied!"));
        }
        break;
    case COPY_IMAGE_TMP:
        init("admin/images");
        $errors = array();
        $result = false;
        if (G::$user->isOrHigher(U_MODERATOR)){
            $filename   = $_POST["img_filename"];
            $type       = $_POST["image_type"];
            $new_filename   = trim($_POST["filename"]);
            $new_filename   = G::$db->quote( strip_tags($new_filename) );
            $new_filename   = preg_replace("/^\'|\'$/", "", $new_filename); // remove side qoutes

            $path = defineImageDir($type);

            if (file_exists(ROOT . $path . $new_filename . ".jpg")){
                $errors[] = RenderUtils::renderError("File with the same name already exists!");
            }

            // TODO preg_match img_filename
            if (!isset($filename) || !strlen($filename)){
                $errors[] = RenderUtils::renderError("Invalid image data!");
            }
            if (!isset($new_filename) || !strlen($new_filename) || !preg_match(LABEL_REG_EXP, $new_filename)){
                $errors[] = RenderUtils::renderError("Invalid filename!");
            }

            if (!count($errors)){
                if (copy(
                    ROOT . "/images/catalogue/_tmp/" . $filename,
                    ROOT . $path . $new_filename . ".jpg")
                ){
                    G::logMessage( "[" . ROOT . "/images/catalogue/_tmp/" . $filename . "] successfully copied to [" . ROOT . $path . $new_filename . ".jpg]");
                    $result = true;
                    $tr = RenderUtils::renderImageDataTableRow("#", $path, $new_filename . ".jpg");
                    unlink(ROOT . "/images/catalogue/_tmp/" . $filename);
                }else{
                    G::logMessage("Failed coping [" . ROOT . "/images/catalogue/_tmp/" . $filename . "] to [" . ROOT . $path . $new_filename . ".jpg]");
                }
            }
        }else{
            $errors[] = RenderUtils::renderError("Access denied!");
        }

        if (count($errors)){
            $result = false;
        }
        $data = array(
            "result" => $result,
            "errors" => $errors,
            "tr" => $tr
        );
        break;
    case DELETE_IMAGE:
        init("admin/images");
        $errors = array();
        $result = false;
        if (G::$user->isOrHigher(U_MODERATOR)){
            $type       = $_POST["image_type"];
            $img_filename   = trim($_POST["img_filename"]);
            $img_filename   = G::$db->quote( strip_tags($img_filename) );
            $img_filename   = preg_replace("/^\'|\'$/", "", $img_filename); // remove side qoutes

            $path = defineImageDir($type);

            $file = ROOT . $path . $img_filename . ".jpg";
            if (!file_exists($file)){
                $errors[] = RenderUtils::renderError("File " . $path . $img_filename . ".jpg does not exist!");
            }else{
                @unlink($file);
                $result = true;
            }
        }else{
            $errors[] = RenderUtils::renderError("Access denied!");
        }

        if (count($errors)){
            $result = false;
        }
        $data = array(
            "result" => $result,
            "errors" => $errors
        );
        break;
}

function defineImageDir($type){
    $path="";
    switch($type){
        default:
        case "pages":
            $path = "/images/pages/";
            break;
        case "c_small":
            $path = "/images/catalogue/small/";
            break;
        case "c_medium":
            $path = "/images/catalogue/medium/";
            break;
        case "c_large":
            $path = "/images/catalogue/large/";
            break;
        case "news":
            $path = "/images/news/";
            break;
        case "articles":
            $path = "/images/articles/";
            break;
    }
    return $path;
}

/* --- compose response --- */
if (!$html){
    G::ajaxResponse($data);
}
?>