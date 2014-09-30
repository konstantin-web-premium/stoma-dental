<?php
include_once "AdminModulesManager.php";
include_once "utils/PageUtils.php";
include_once "utils/CatalogueUtils.php";
include_once "utils/AdminUtils.php";
include_once "utils/RenderUtils.php";
include_once "catalogue/CurrencyConverter.php";
include_once "catalogue/ProductItem.php";
include_once "catalogue/BrandEntity.php";
include_once "catalogue/Paginator.php";
include_once "catalogue/PropsManager.php";

class PageData{
    private $address;
    private $modulesManager;
    private $inited = false;
    private $errors = array();
    private $sidebar_left_inited = 0; // 0 - not inted, 1 - inited open, 2 - inited closed
    private $sidebar_right_inited = 0;

    public $data;
    public $propsManager;
    public $currencyConverter;
    public $imagesType;

    protected $news; // = array();
    protected $promos; // = array();
    protected $blocks = array();
    protected $brands = array();
    protected $settings = array();
    protected $menu = array();
    protected $socials = array();
    protected $currency = array();
    //protected $browser = array();

    public function __construct($pageLabel, $pageGet = ""){
        // TODO browser data
        // $browser = $this->getBrowserData();

        $this->init($pageLabel);

        # admin modules manager
        $this->modulesManager = new AdminModulesManager($this->address["page"]);

        $this->loadConfigs();
        # currency convertor. needs config's currency
        $this->currencyConverter = new CurrencyConverter($this->currency);

        $this->propsManager = new PropsManager();

        $this->inited = true;
    }


// ------------------------------------------------------------
// PRIVATE ----------------------------------------------------
// ------------------------------------------------------------
    private function init($pageLabel){
        # validate address string
        $pageLabel = PageUtils::validatePageLabel($pageLabel);  // valid string

        # set session address props
        $this->setSessionPageData($pageLabel);

        # Parsing address AND set category
        $this->address = PageUtils::parsePageLabel($pageLabel);
    }

    private function loadConfigs(){
        // PAGE BLOCKS
        $this->blocks = PageUtils::getBlocks();
        // SETTINGS
        $this->settings = PageUtils::getSettings();
        // MENUs
        $this->menu = PageUtils::getAllMenu();
        // Currency
        $this->currency = PageUtils::getAllCurrency();
        // NEWS
        $this->initNewsData();

    }

    private function setSessionPageData($pageLabel){
        if (isset($_SESSION["current_page"])){
           $_SESSION["prev_page"] = $_SESSION["current_page"];
        }
        $_SESSION["current_page"] = $pageLabel;
        if (!isset($_SESSION["page_items_limit"])){
            $_SESSION["page_items_limit"] = Paginator::PAGE_ITEMS_LIMIT;
        }
    }


    private function getBrowserData(){
        $browser = array();
        // TODO browser settings
        return $browser;
    }

    private function loadPageData(){
        if (!$this->inited){
            G::fatalError("PageData::INTERNAL_ERROR");
        }

        unset($this->data);
        G::logMessage(">>" . $this->address["type"] . " -- " . $this->address["full_address"]);
        switch($this->address["type"]){
            default:
            case P_TYPE_PAGE:
                $this->data = $this->getPageItem($this->address["page"]);
                G::addToRender("<h1>" . $this->data["title"] . "</h1>", BLOCK_CONTENT);
                break;
            case P_TYPE_NEWS:
                $this->data = $this->getNewsItem($this->address["page"]);
                G::addToRender("<h1 class='news-title'>" . $this->data["short"] . "</h1>", BLOCK_CONTENT);
                G::addToRender("<div class='news-date'>" . $this->data["date"] . "</div>", BLOCK_CONTENT);
                // DEBUG

                $this->data["access"] = 4;
                $this->data["blocks_id"] = "1,2,3,4,5,6,7,8,9,10,11,12,15";

                //
                break;
            case P_TYPE_BRAND:
                $this->data = $this->getBrandItem($this->address["page"]);
                G::addToRender("<h1 class='news-title'>" . $this->data["brand_full_name"] . "</h1>", BLOCK_CONTENT);
                G::addToRender("<div class='news-date'>" . $this->data["country_name"] . "</div>", BLOCK_CONTENT);
                G::addToRender("<img class='news-page-title-img' src='" . $this->data["logo_medium_url"] . "' />", BLOCK_CONTENT);
                // DEBUG

                $this->data["access"] = 4;
                $this->data["blocks_id"] = "1,2,3,4,5,6,7,8,9,10,11,12,15";

                //
                break;
            case P_TYPE_TECH:
                $this->data = $this->getTechPageItem();
                break;
            case P_TYPE_ADMIN:
                $this->data = $this->getAdminPageItem();
                // DEBUG

                $this->data["access"] = 4;
                $this->data["blocks_id"] = "1,2,3,4,5,6,7,8,9,10,11,12";

                //
                break;
            case P_TYPE_CATALOGUE:
                $this->propsManager = new PropsManager();
                $this->data = $this->getCatalogueItem($this->address["path"]);
                break;
            case P_TYPE_ARTICLES:
                break;
        }

        $this->initAddonBlocks();

        return isset($this->data);
    }

    private function initAddonBlocks(){
        // PROMO
        if ($this->isVisibleBlock(BLOCK_PROMO)){
            G::addToRender("blocks/side_promo.php", BLOCK_SIDEBAR_RIGHT, CODE);
        }
        if ($this->address["type"] == P_TYPE_CATALOGUE && count($this->address["path"]) > 1){
            G::addToRender("catalogue/address_line.php", BLOCK_PRE_CONTENT, CODE);
        }
        // RECENT
        if ($this->isVisibleBlock(BLOCK_RECENT)){
            //G::addToRender("blocks/content_promo_block.php", BLOCK_RECENT, CODE);
        }
        // NEWS
        if ($this->isVisibleBlock(BLOCK_NEWS) || $this->isVisibleBlock(BLOCK_CAROUSEL)){
            if ($this->isVisibleBlock(BLOCK_NEWS)){
               G::addToRender("blocks/side_news.php", BLOCK_SIDEBAR_RIGHT, CODE);
            }
        }
        // NEWS  on the LEFT sidebar
        G::addToRender("blocks/sidebar_brands.php", BLOCK_SIDEBAR_LEFT, CODE);

    }

    private function initNewsData(){
        if (!$this->news){
            $this->news = PageUtils::getNews();
        }
    }

    private function getCatalogueItem($path){
        $data = array();
        $path_items = CatalogueUtils::getPathItems($path);

        $catalogue = array();
        $catalogue["path_items"] = $path_items;

        $last = count($path) - 1;
        $catalogue["current_label"] = $path[$last];

        /*
         * // prev item categories list
         *
        $arr = $this->address["path"];
        array_pop($arr);
        $catalogue["prev_item_path"] = implode("/", $arr);

        $pre_last = count($this->address["path"]) - 2;
        if ($pre_last >= 0){
            $childrenId = $path_items[$pre_last]["children_id"];
            $current_categories = CatalogueUtils::getProducts($childrenId);
        }
        */

        if (count($path) > 1){
            $childrenId = $path_items[0]["children_id"];
            $current_categories = CatalogueUtils::getProducts($childrenId);

            G::addToRender("blocks/sidebar_navigation.php", BLOCK_SIDEBAR_LEFT, CODE);
        }else{
            G::addToRender("<h1>" . G::$language->getText("common", "catalogue_link_text") . "</h1>", BLOCK_CONTENT);
        }

        $childrenId = $path_items[$last]["children_id"];

        $products = array();
        if ($path_items[$last]["type"] != TYPE_CATEGORY){
            // single product
            $node = CatalogueUtils::getCatalogueNode($path_items[$last]["label"]);
            $current_product = new ProductItem($node); # required class ProductItem
            $data = $this->parsePageData($node);
            // "if hidden" HERE because - admin mode should load data of hidden elements
            if (!$path_items[$last]["hidden"]){
                G::addToRender("catalogue/catalogue_single_product.php", BLOCK_CONTENT, CODE);
                G::addToRender("blocks/brand_block.php", BLOCK_SIDEBAR_RIGHT, CODE);
            }else{
                //$this->redirect(PAGE_PAGE404); when not AJAX
                $message = "<p class='grey-text' align='center'>" . G::$language->getText("catalogue", "category_fail") . "</p>";
                G::addToRender( $message, BLOCK_CONTENT);
            }
        }else{
            $node = CatalogueUtils::getCatalogueNode($path_items[$last]["label"]);
            $current_product = new ProductItem($node);
            $data = $this->parsePageData($node);
            if (strlen($childrenId) > 0){
                //multiple categories|products
                $total = count(explode(",", $childrenId));
                $page = intval($_GET["page"]);
                $items_limit = (isset($_SESSION["page_items_limit"]) ? $_SESSION["page_items_limit"] : Paginator::PAGE_ITEMS_LIMIT);
                $max_pages = ceil($total / $items_limit);
                if ($page > $max_pages){
                    $_GET["page"] = $page = $max_pages;
                }
                if ($total > $_SESSION["page_items_limit"]){
                    $paginator = new Paginator($max_pages, $page);
                    G::addToRender($paginator->render(), BLOCK_CONTENT, TEXT);
                }
                $products = CatalogueUtils::getProducts($childrenId, $_SESSION["page_items_limit"], $page);
            }
            if ($products && count($products) > 0){
                G::addToRender("catalogue/catalogue_page_blocks.php", BLOCK_CONTENT, CODE);
//                die("HERE:".count($products));
            }else{
                // no products in category
                $message = "<p class='grey-text' align='center'>" . G::$language->getText("common", "empty_category") . "</p>";
                G::addToRender( $message, BLOCK_CONTENT);
            }
        }

        $data["current_categories"] = $current_categories;
        $data["catalogue"] = $catalogue;
        $data["products"] = $products;
        $data["current_product"] = $current_product;

        // DEBUG

        $data["access"] = 4;
        if (!isset($data["title"])){
            $data["title"] = G::$language->getText("common", "catalogue_link_text");
        }
        $data["blocks_id"] = "1,3,4,5,6,7,8,9,10,11,12,15";

        //

        return $data;
    }

    private function getAdminPageItem(){
        $adminModule = $this->getCurrentAdminModule();
        if ($adminModule){
            $adminModule->init($this->address);
            $data = $adminModule->getData();
        }

        return $data;
    }

    private function getTechPageItem(){
        $data = array();
        switch($this->address["page"]){
            case PAGE_INDEX:
                $data = $this->getPageItem();
                $addon_data = $this->getCatalogueItem(array("catalogue"));
                    $data["current_categories"] = $addon_data["current_categories"];
                    //$data["content"]            = $addon_data["content"];
                    $data["catalogue"]          = $addon_data["catalogue"];
                    $data["products"]           = $addon_data["products"];
                break;
            case PAGE_LOGOUT:
                G::$user->logout();
                $this->redirectPrev();
                break;
            case PAGE_LOGIN:
                $result = G::$user->loginUser($_POST["login"], $_POST["password"]);
                if (!$result)
                {
                    G::addToRender("Authorization FAILED!", BLOCK_CONTENT);
                }else{
                    if ($_SESSION["prev_page"] != PAGE_ACCESS_DENIED){
                        $this->redirectPrev();
                    }else{
                        $this->redirect(PAGE_INDEX);
                    }
                }
                break;
            case PAGE_REGISTER:
                $data = $this->getPageItem();
                if (isset($_POST['submit'])){
                    G::$user->registerUser($_POST);
                }
                G::addToRender("blocks/register.php", BLOCK_CONTENT, CODE);
                break;
            case PAGE_ENTER:
            case PAGE_ACCESS_DENIED:
                $data = $this->getPageItem();
                G::addToRender("forms/login_form.php", BLOCK_CONTENT, CODE);
                break;
            default:
                // if nothing not found TECHNICAL page been identified mistacely
                unset($data);
                break;
        }

        return $data;
    }

    private function getPageItem($page = ""){
        if ($page == ""){
            $page = $this->address["page"];
        }
        $pageNode = PageUtils::getPageNode($page);
        $data = $this->parsePageData($pageNode);
        return $data;
    }

    private function getNewsItem($news_label = ""){
        if ($news_label == ""){
            $news_label = $this->address["page"];
        }
        $pageNode = $this->getNewsByLabel($news_label);
        $data = $this->parsePageData($pageNode);
        $data["content"] = strlen($data["full"]) ? $data["full"] : $data["short"];
        return $data;
    }

    private function getBrandItem($brand_label = ""){
        if ($brand_label == ""){
            $brand_label = $this->address["page"];
        }
        $brand = $this->getBrandByLabel($brand_label);
        if ($brand){
            $url = strlen($brand->url) ? "<a href='$brand->url'>$brand->name �� �����</a>" : "";
            G::logMessage("Brand '$brand_label' found!");
            $data = array(
                "id" => $brand->id,
                "label" => $brand->label,
                "brand_name" => $brand->name,
                "brand_full_name" => $brand->full_name,
                "logo_small_url" => $brand->getLogoUrl("small"),
                "logo_medium_url" => $brand->getLogoUrl("medium"),
                "logo_large_url" => $brand->getLogoUrl("large"),
                "country_name" => $brand->getCountry(),
                "content" => $brand->description . (strlen($url) ? "<p>$url</p>" : "")
            );
        }else{
            G::logMessage("Brand '$brand_label' not found");
        }
        return $data;
    }

    private function parsePageData($node){
        if ($node){
            $data = array();
            foreach($node  as $key => $value){
                if (!preg_match("/^og_/", $key)){
                    $data[$key] = $value;
                }else{
                    $subkey = substr($key, 3);
                    $data["og"][$subkey] = $value;
                }
            }
        }

        return $data;
    }

    /** Gets NEWS array BY its label
     * @param $label
     * @return array | null
     */
    private function getNewsByLabel($label){
        if (count($this->news)){
            foreach($this->news as $news){
                if ($label == $news["label"]){
                    return $news;
                }
            }
        }
        if (count($this->promos)){
            foreach($this->promos as $news){
                if ($label == $news["label"]){
                    return $news;
                }
            }
        }
        return null;
    }

    /** Gets BRAND ENTITY BY its label
     * @param $label
     * @return BrandEntity | null
     */
    private function getBrandByLabel($label){
        if (count($this->brands)){
            foreach($this->brands as $item){
                if ($label == $item->label){
                    return $item;
                }
            }
        }
        return null;
    }

    private function redirect($location){
        if (G::$dataType == G::DATATYPE_AJAX){
            G::fatalError("PageData::redirect() : Try to redirect to '" . $location . "' while ajax request!");
        }

        if ($_SESSION["current_page"] == $location)
        {
            G::fatalError("PageData::redirect() : ENDLESS CYCLE! at try to redirect to '/$location'");
        }
        header("Location: /$location");
        exit();
    }

    private function renderJS(){
        // hard code js
        $js_files = array();
        array_push($js_files,
                // alt =======> http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js
                // http://code.jquery.com/jquery-2.1.0.min.js
                "jquery.js",
                "main.js",
                "jquery.checkbox.js",
                "jquery.spoiler.js",
                "jquery.inputhints.js"
                # "jquery.cookie.js",
        );
        // Admin modules JS
        $filenames = $this->modulesManager->requiredJSFiles;
        if (count($filenames)){
            foreach($filenames as $filename){
                $js_files[] = $filename;
            }
        }
        // pre render all
        if (count($js_files)){
            foreach($js_files as $filename){
                G::addToRender(RenderUtils::renderScriptLink($filename), BLOCK_HEAD);
            }
        }
    }

    private function renderCSS(){
        $css_files = array();
        $css_files[] = CSS_FILE;
        // Admin modules JS
        $filenames = $this->modulesManager->requiredCSSFiles;
        if (count($filenames)){
            foreach($filenames as $filename){
                $css_files[] = $filename;
            }
        }
        // pre render all
        if (count($css_files)){
            foreach($css_files as $filename){
                G::addToRender(RenderUtils::renderCSSLink($filename), BLOCK_HEAD);
            }
        }
    }

    private function validatePageData($data, $type = ""){
        $errors = array();
        if ($type == ""){
            $type = $this->address["type"];
        }
        switch($type){
            default:
            case P_TYPE_PAGE:
            case P_TYPE_TECH:
                $t_data = TABLE_PAGES;
                $requiredFields = array("access", "blocks_id", "label", "title", "content");
                break;
            case P_TYPE_CATALOGUE:
                $t_data = TABLE_CATALOGUE;
                $requiredFields = array("label", "title");
                break;
            case P_TYPE_ARTICLES:
                $t_data = TABLE_ARTICLES;
                $requiredFields = array("label", "title");
                break;
        }
        $t_meta = TABLE_META;

        //G::logMessage("PageData::validatePageData() > tablename=$t_data type=" . $type);
        $keys = PageUtils::getTableKeys($t_data);                      // get keys
        $keys = array_merge($keys, PageUtils::getTableKeys($t_meta));   // get meta keys
        $keys = array_unique($keys);                                    // remove same
        ### array_unique() leave EMPTY KEYs in array with EMPTY value such as ''=>''
        for($i = count($keys)-1; $i >= 0; $i--){
            if (trim($keys[$i])==''){
                unset($keys[$i]); // DO NOT use splice, $i <> iterator !!!
            }
        }

        # BLOCK_ID serialized from $_POST["block_%"] values
        $arr = array();
        foreach($data as $key=>$value){
            if (preg_match("/^block_[\d]+$/", $key) && $value == "true"){
                $id = preg_replace("/^block_/", "", $key);
                $arr[] = $id;
                unset($data[$key]);
            }
        }
        $data["blocks_id"] = implode(",", $arr);

        // clean from other keys

        foreach($data as $key=>$value){
            if (!in_array($key, $keys)){
                unset($data[$key]);
            }
        }

        // if id==0 remove it as unset. means create new NODE
        if ($data["id"] == "0"){
            unset($data["id"]);
            unset($data["meta_id"]);
        }else{
            $requiredFields[] = "meta_id";
        }

        for($i = count($keys)-1; $i >= 0; $i--){
            $key = $keys[$i];
            if (!isset($data[$key])){
                continue;
            }
            $data[$key] = trim($data[$key]);
            // empty parameter
            if ($data[$key] == ""){
                if(in_array($key, $requiredFields)){
                    $errors[] = RenderUtils::renderError("$key field is required");
                }
                continue;
            }
            // validation result
            $validation = PageUtils::validatePageParam($data[$key], $key);
            if ($validation["result"]){
                $data[$key] = $validation["value"];
            }else{
                $errors[] = RenderUtils::renderError($validation["value"]);
                unset($data[$key]);
            }
        }

        if (!isset($data["id"]) && PageUtils::labelExists($data["label"], $t_data)){
            $errors[] = RenderUtils::renderError("Label exists!");
        }

        $this->errors = $errors;
        return $data;
    }


    private function renderMeta(){
        if(G::$user->isOrHigher(MODERATOR)){
            $filename = ADMIN_META_FILE;
        }else{
            $filename = META_FILE;
        }
        G::addToRender($filename, BLOCK_HEAD, CODE);
    }

    private function checkNewLabel($id, $newLabel){
        $idByLabel = PageUtils::getIdByLabel($newLabel, TABLE_CATALOGUE);
        //G::logMessage("checkNewLabel >> id = '$id', newLabel = '$newLabel', idByLabel = '$idByLabel';");
        if ($idByLabel > 0 && $idByLabel != $id){
            return false;
        }
        return true;
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    public function getCurrentAdminModule(){
        return $this->modulesManager->getCurrent();
    }

    public function load(){

        $result = $this->loadPageData();

        // page not found
        if(!$result){
            # if ajax return error
            if (G::$dataType == G::DATATYPE_AJAX){
                G::fatalError("Page '" . $this->getAddress() . "' not found!");
            }
            # index not found
            if($_SESSION["current_page"] == PAGE_INDEX){
                G::fatalError("INDEX NOT FOUND :: ".DATABASE_ERROR_MESSAGE);
            }
            # 404 not found
            if($_SESSION["current_page"] == PAGE_PAGE404){
                $this->redirect(PAGE_INDEX);
                exit();
            }
            # regular page not found
            $this->redirect(PAGE_PAGE404);
            exit();
        }

        // USER have access?
        if (!G::$user->isOrHigher($this->data["access"])){
            if (G::$dataType == G::DATATYPE_AJAX){
                G::fatalError("Access denied!");
            }
            else
            {
                // MAy NOT REDIRECT BUY RELOAD A_D!!!!!!!!!!!!!!!!!!!!!!!!!!
                $this->redirect(PAGE_ACCESS_DENIED);
                exit();
            }
        }

        // SPEC access
        if (G::$user->isOrHigher(U_MODERATOR)){
            $this->modulesManager->init();
        }

        /* FINALIZE load ---------------------- */
        # content
        if ($this->getAddress("type") != P_TYPE_CATALOGUE){

            G::addToRender("<div class='content-white-back'>", BLOCK_CONTENT);
            G::addToRender($this->data["content"], BLOCK_CONTENT);
            G::addToRender("</div>", BLOCK_CONTENT);
        }
        G::addToRender(META_FILE, HEAD, CODE);
        $this->renderMeta();
        # css
        $this->renderCSS();
        # js
        $this->renderJS();
    }

    /** CREATE or UPDATE page node
     * @param $data - page data
     * @return bool
     */
    public function update($data){
        //reinit
        $this->init($data["label"]);
        //
        $data = $this->validatePageData($data);
        $result = false;
        if (count($this->errors) == 0){
            if (isset($data["id"])){
                // UPDATE PAGE
                $result = PageUtils::updatePageNode($data["id"], $data);
            }else{
                // CREATE NEW PAGE
                $result = PageUtils::createPageNode($data);
            }

            // check db errors
            if (intval(G::$db->errorCode()) != 0){
                $error_info = G::$db->errorInfo();
                $text_error = " DB >> " . $error_info[1] . " > " . $error_info[2];
                $this->errors[] = RenderUtils::renderError($text_error);
            }

            if (count($this->errors) == 0 && $result){
                $pageNode = PageUtils::getPageNode($data["label"]);
                $this->data = $this->parsePageData($pageNode);
            }
        }
        return $result;
    }

    /** CREATE or UPDATE catalogue node
     * @param $data - product\category data
     * @return bool
     */
    public function updateCatalogue($data){
        // rename item_type (client vars conflict)
        $data["type"] = $data["item_type"];
        unset($data["item_type"]);
        //

        // parent_id
        $validation = PageUtils::validatePageParam($data["parent_id"], "parent_id");
        if (!$validation["result"]){
            G::fatalError("PARENT_ID is not valid");
        }
        unset($data["parent_id"]);
        $parent_id = $validation["value"];
        //

        // images
        G::logMessage("IMAGES: " . "small=" . $data["img_small"] . "&medium=" . $data["img_medium"] . "&large=" . $data["img_large"]);
        $data["image"] = "small=" . $data["img_small"] . "&medium=" . $data["img_medium"] . "&large=" . $data["img_large"];
        unset($data["img_small"],$data["img_medium"],$data["img_large"]);
        //

        $data = $this->validatePageData($data, P_TYPE_CATALOGUE);

        //check if label already exists
        if (!$this->checkNewLabel($data["id"], $data["label"])){
            $this->errors[] = "Label already exists in another product";
        }

        $result = false;
        if (count($this->errors) == 0){

            if (isset($data["id"])){
                // UPDATE PAGE
                $result = CatalogueUtils::updateCatalogueNode($data["id"], $data);
                $parents = CatalogueUtils::findParentsOf($data["id"]);
                $parentAlreadyHasIt = false;
                foreach($parents as $parent){
                    if ($parent["id"] != $parent_id){
                        CatalogueUtils::deleteChildFromParent($data["id"], $parent["id"]);
                    }else{
                        $parentAlreadyHasIt = true;
                    }
                }
                if (!$parentAlreadyHasIt && $parent_id){
                    CatalogueUtils::addChildToParent($data["id"], $parent_id);
                }

            }else{
                // CREATE NEW NODE
                $result = CatalogueUtils::createCatalogueNode($data, $parent_id);
            }

            // check db errors
            if (intval(G::$db->errorCode()) != 0){
                $error_info = G::$db->errorInfo();
                $text_error = " DB >> " . $error_info[1] . " > " . $error_info[2];
                $this->errors[] = RenderUtils::renderError($text_error);
            }

            if (count($this->errors) == 0 && $result){
                //reinit
                $this->init("catalogue/" . $data["label"]);
                $this->load();
            }
        }
        return $result;
    }

    /** DELETE page node
     * @return bool
     */
    public function delete($label){
        $result = false;
        $errors = array();
        $label = $this->data["label"];
        if (!isset($label)){
            $errors[] = RenderUtils::renderError("Label undefined!");
        }else
        if (!PageUtils::validatePageParam($label, "label")){
            $errors[] = RenderUtils::renderError("Label is invalid!");
        }else
        if (!PageUtils::labelExists($label)){
            $errors[] = RenderUtils::renderError("Label does not exist!");
        }else{}
        if (PageUtils::isTechnicalPage($label)){
            $errors[] = RenderUtils::renderError("This is technical page!");
        }
        //
        if (count($errors) == 0){
            # TODO delete meta node

            // DELETE PAGE
            $result = PageUtils::deletePageNode($label);

            // check db errors
            if (intval(G::$db->errorCode()) != 0){
                $error_info = G::$db->errorInfo();
                $text_error = " DB >> " . $error_info[1] . " > " . $error_info[2];
                $this->errors[] = RenderUtils::renderError($text_error);
            }
        }
        return $result;
    }


    /**
     * Redirect to previous page
     */
    public function redirectPrev(){
        if (isset($_SESSION["prev_page"]) && $_SESSION["prev_page"]!=$_SESSION["current_page"]){
            $this->redirect($_SESSION["prev_page"]);
        }
        $this->redirect(PAGE_INDEX);
    }

    /**
     * @param $prop - see config.php >> SETTINGS
     * @return string
     */
    public function getSetting($prop){
        return $this->settings[$prop];
    }

    /**
     * @param $id - brand_id
     * @return BrandEntity
     */
    public function getBrand($id){
        return $this->brands[$id];
    }

    /**
     * @return array( [BrandEntity] );
     */
    public function getBrands(){
        if (!$this->brands){
            $this->brands = PageUtils::getBrands();
        }
        return $this->brands;
    }

    /**
     * @return array ( id, label, description )
     */
    public function getBlocksList(){
        return $this->blocks;
    }

    /**
     * @return array
     */
    public function getNewsList(){
        return $this->news;
    }

    /**
     * @return array
     */
    public function getPromoList(){
        return $this->promos;
    }

    public function getSocials(){
        if (!$this->socials){
            $this->socials = PageUtils::getSocials();
        }
        return $this->socials;
    }

    public function isVisibleBlock($label){
        if (!isset($this->data["blocks_id"]))
        {
            return false;
        }
        $blocks_id = preg_split("/,/", $this->data["blocks_id"]);
        foreach($this->blocks as $block){
            if ($block["label"] == $label
                &&
                in_array($block["id"], $blocks_id)){
                return true;
            }
        }
        return false;
    }

    public function getMenu($title){
        return $this->menu[$title];
    }

    public function getAddress($var = "address"){
        return $this->address[$var];
    }

    public function getCurrency(){
        return $this->currency;
    }

    /**
     * @param $type - pages|articles|catalogue
     * @return mixed
     */
    public function getItemsList($type){
        if (!isset($this->lists)){
            $this->lists = array();
        }
        if (!isset($lists[$type])){
            $lists[$type] = PageUtils::getItemsList($type);
        }
        return $lists[$type];
    }

    /** getter
     * @return array
     */
    public function getErrors(){
        return $this->errors;
    }

    public function __ToString(){
        return "[PageData class]";
    }
}
?>