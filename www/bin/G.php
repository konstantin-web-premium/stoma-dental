<?php
include_once $_SERVER["DOCUMENT_ROOT"] . PATH_EXTERNAL . "json/JSON.php";
include_once $_SERVER["DOCUMENT_ROOT"] . PATH_EXTERNAL . "kcaptcha/kcaptcha.php";

class G{
    const DATATYPE_NORMAL = "normal";
    const DATATYPE_AJAX = "ajax";
    const DATATYPE_AJAX_HTML = "ajax_html";

    public static $isRendering = false;
    public static $db;
    public static $pageData;
    public static $user;
    public static $blocks;
    public static $language;
    public static $modules;
    public static $dataType = self::DATATYPE_NORMAL;
    public static $ajaxData;

    private static $JSON;
    private static $sidebar_left_inited = 0;
    private static $sidebar_right_inited = 0;

// -----------------------------------------------------------
// PRIVATE ----------------------------------------------------
// -----------------------------------------------------------

    private static function getJSON(){
        if (!self::$JSON){
            self::$JSON = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        return self::$JSON;
    }

    /** LEFT sidebar INIT and FINALIZE
     * @param string $action
     */
    private static function sidebarLeftActivate($action = "open"){
        if($action == "open"){
            // OPEN
            if (self::$sidebar_left_inited == 0){
                self::$sidebar_left_inited = 1;
                self::addToRender("<div class=\"sidebar-left\">\n", BLOCK_SIDEBAR_LEFT);
            }
        }else{
            // CLOSE
            if (self::$sidebar_left_inited == 1){
                self::$sidebar_left_inited = 2;
                self::addToRender("</div><!-- sidebar-left -->\n", BLOCK_SIDEBAR_LEFT);
            }
        }
    }

    /** RIGHT sidebar INIT and FINALIZE
     * @param string $action
     */
    private static function sidebarRightActivate($action = "open"){
        if($action == "open"){
            // OPEN
            if (self::$sidebar_right_inited == 0){
                self::$sidebar_right_inited = 1;
                self::addToRender("<div class=\"sidebar-right\">\n", BLOCK_SIDEBAR_RIGHT);
            }
        }else{
            // CLOSE
            if (self::$sidebar_right_inited == 1){
                self::$sidebar_right_inited = 2;
                self::addToRender("</div><!-- sidebar-right -->\n", BLOCK_SIDEBAR_RIGHT);
            }
        }
    }

// -----------------------------------------------------------
// PUBLIC ----------------------------------------------------
// -----------------------------------------------------------
    /**
     * MAIN connection to DB
     */
    public static function connect($server, $username, $password, $db_name, $convert = true){
        $connect_str = "mysql:host=" . $server . ";dbname=" . $db_name;
        self::$db = new PDO($connect_str, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

        //if ($convert){
			//self::$db->exec('SET NAMES UTF-8');
        //}
    }

    /** Log in file on server
     * @param $message
     */
    public static function logMessage($message){
        $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/bin/log.txt", "a");
        fwrite($file, date("h:i:s") . ": " . $message . "\r\n");
        fclose($file);
    }

    /** Data types - see config.php >> RENDER DATA TYPES
     * @param $blockName - see config.php >> RENDER Blocks
     */
    public static function render($blockName){
        self::sidebarRightActivate("close");    // close sidebar div if activated
        self::sidebarLeftActivate("close");     // close sidebar div if activated

        self::$isRendering = true;

        if (isset(self::$blocks[$blockName])
            &&
            count(self::$blocks[$blockName]) > 0
            &&
            self::$pageData->isVisibleBlock($blockName)
        )
        {
            echo "<!-- $blockName _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ -->\n";
            foreach (self::$blocks[$blockName] as $renderData)
            {
                switch($renderData["type"]){
                    case TEXT:
                        print $renderData["data"]."\n";
                        break;
                    case FILE:
                        # TODO copy from file
                        break;
                    case CODE:
                        # TODO check file exists
                        include $_SERVER["DOCUMENT_ROOT"] . $renderData["data"];
                        break;
                }
            }
        }

        self::$isRendering = false;
    }

    /**
     * @param string $data - plain text  OR filename
     * @param string $block - see config.php >> RENDER Blocks
     * @param string $dataType - see config.php >> RENDER DATA TYPES
     */
    public static function addToRender($data, $blockName = CONTENT, $dataType = TEXT){
        if (!self::$blocks && G::$dataType == G::DATATYPE_NORMAL){
            die("(G)addToRender : Blocks not inited yet!");
        }
        if (self::$isRendering){
            die("(G)addToRender : Use addToRender() BEFORE render()");
        }

        if (isset(self::$blocks[$blockName])){
            // Activate sidebars before write into them
            if ($blockName == BLOCK_SIDEBAR_RIGHT){
                self::sidebarRightActivate("open");
            }
            if ($blockName == BLOCK_SIDEBAR_LEFT){
                self::sidebarLeftActivate("open");
            }
            //

            $data = ($dataType != TEXT ? PATH_INCLUDES : "").$data;
            $renderData = array("type" => $dataType, "data" => $data);
            array_push(self::$blocks[$blockName], $renderData);
        }

    }

    /** Defines blocks list
     * @param $blockList
     */
    public static function initPageRender(){
        self::$blocks = array();

        $blockList = self::$pageData->getBlocksList();
        foreach($blockList as $block){
            self::$blocks[$block["label"]] = array();
        }
    }

    /** TERMINATE execution AND log message
     * @param $message
     */
    public static function fatalError($message){
        $ajax = (self::$ajaxData["actionId"] ? true : false);
        $err_info = G::$db->errorInfo();

        self::logMessage(
            "ERROR : " .
            ($ajax
                ? "(ajax request:" . self::$ajaxData["action"] . ") "
                : "") .
            $message . "\n" .
            "DB info: $err_info[0]; $err_info[1]; $err_info[2]"
        );
        switch(self::$dataType){
            case self::DATATYPE_NORMAL:
                die($message);
                break;
            case self::DATATYPE_AJAX:
                $errors = array($message);
                if (self::$ajaxData["actionId"]){
                    self::ajaxResponse(null, $errors);
                }else{
                    //SIMPLE HTML
                    echo "FATAL ERROR: " . $message;
                }
                die();
                break;
        }
    }

    /** GENERATES ajax-formatted response
     * render ajax formatted data
     */
    public static function ajaxResponse($data, $errors = null){
        $response = array(
            "data"      => $data,
            "actionId"  => self::$ajaxData["actionId"],
            "errors"    => $errors
        );

        $JSON = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

        $json_str = self::serialize($response);
        //G::logMessage("JSON RESPONSE => " . $json_str);
        echo $json_str;
    }

    /** JSON serialize
     * @param $data
     * @return mixed
     */
    public static function serialize($data){
        return self::getJSON()->encode($data);
    }

    /** FORMATS data to string for MYSQL UPDATE
     * @param $data
     * @return string
     */
    public static function serializeUpdate($data){
        $arr = array();
        foreach($data as $key=>$value){
            // ATTENTION! bullshit happens when $value=1 -> $value == "null" return true!
            $arr[] = "$key=" . (($value == null || $value."" == "null") ? "null" : "'$value'");
        }
        return implode(",", $arr);
    }

    /** FORMATS data to string for MYSQL INSERT
     * @param $data = all key=>values
     * @param $tables - array( "table_name", ... )
     * @return array( table_name => serialized_data )
     */
    public static function serializeInsert($data, $table_name){
        $table_keys = PageUtils::getTableKeys($table_name);
        $keys = array();
        $values = array();
        foreach($data as $key=>$value){
            // ATTENTION! bullshit happens when $value=1 -> $value == "null" return true!
            if(in_array($key, $table_keys) && $value != null &&  $value."" != "null"){
                $keys[] = $key;
                $values[] = $value;
            }
        }
        return "(" . implode(",", $keys) . ") VALUES ('" . implode("','", $values). "')";
    }

    /** IS local debug?
     * @return bool
     */
    public static function isLocal(){
        return $_SERVER["SERVER_ADDR"] == "127.0.0.1";
    }
}
?>