/* CONSTANTS -------------------------------------------------------- */
var CSS_INPUT_GREY = {
    color : "#999",
    background : "#fff"
}

var CSS_INPUT_ERROR = {
    background : "#fee",
    color : "#000"
}

var CSS_INPUT_NORMAL = {
    color : "#333",
    background : "#fff"
}



/* ---------------------------------------------------------------------------- */
/* CLASSES emulating ---------------------------------------------------------- */
/* ---------------------------------------------------------------------------- */




/* InputManager ============================================================================= */



var InputManager = function(){
}


/** insert text with field init*/
InputManager.prototype.insertIntoInput = function($obj, text){
    $obj.val(text)
        .attr("inited", "true")
        .inputHints();
}

InputManager.prototype.clear = function($obj){
    $obj.val("")
        .inputHints();
}

/** Mark error fields red */
InputManager.prototype.setInputErrors = function(errors_key, form_id){
    var $inputs;
    if (!form_id){
        $inputs = $("input");
    }else{
        $inputs = $("#" + form_id + " input");
    }
    for(var key in errors_key){
        $inputs.attr("error", (errors_key[key] ? "1" : "0"))
            .inputHints();
    }
}




/* AjaxManager ============================================================================= */


var AjaxManager = function(service_url){
    this.url        = service_url;
    this.action     = "";
    this.data       = new Object();
    this.type       = "POST";
    this.queue      = new Array();
    this.callback   = function(){};
}

AjaxManager.prototype.send = function(action, callback, data, html){
    if (_debug){
        html = true;
    }
    if (html){
        this.callback = callback;
    }
    else
    {
        var queue_item =  {
            id       : this.createId(),
            callback : callback,
        }
        this.queue.push(queue_item);
    }

    var data_string = "action=" + action;
    if (html){
        data_string += "&html=" + html.toString();
    }
    if (queue_item){
        data_string += "&actionId=" + queue_item.id;
    }
    if (data){
        if (typeof data == "string"){
            data_string += "&" + data;
        }
        else
        {
            data_string += this.implodeData(data);
        }
    }

    var dataType = (html ? "html" : "json");

    $.ajax({
        url     : this.url,
        type    : this.type,
        dataType: dataType,
        async   : !html,
        data    : data_string,
        success : function(data){
            if (dataType == "json"){
                ajaxManager.onSuccess(data);
            }
            else
            {
                ajaxManager.onHTMLSuccess(data);
            }
        },
        error   : function(jqXHR, textStatus, errorThrown){
            ajaxManager.onError(jqXHR, textStatus, errorThrown);
        }
    });
}

AjaxManager.prototype.onSuccess = function(response){
    var id = response.actionId;
    var callback = null;
    for(var i = this.queue.length - 1; i >= 0; i--){
        if (this.queue[i].id == id){
            callback = this.queue[i].callback;
            this.queue.splice(i, 1);
        }
    }
    if(response.errors && response.errors.length > 0){
        this.showErrors(response.errors);
        return;
    }
    if (callback && response.data){
        callback(response.data);
    }
}

AjaxManager.prototype.showErrors = function(errors){
    // TODO AJAX popup MESSAGE
    var str = "Errors:\n";
    for (var i = 0; i<errors.length; i++){
        str += errors[i]+"\n";
    }
    console.log("AjaxManager::showErrors():\n" + str);
}

AjaxManager.prototype.onHTMLSuccess = function(response){
    if (_debug){
        console.log("AjaxManager::onHTMLSuccess():\n" + response);
    }
    if (this.callback){
        this.callback(response);
    }
}

AjaxManager.prototype.onError = function(jqXHR, textStatus, errorThrown ){
    console.log("AjaxManager::onError():\n" + "error => ["+textStatus+"] "+errorThrown);
}

AjaxManager.prototype.createId = function(){
    var rand = Math.random() * 10000 + 10;
    rand = Math.floor(rand);
    var id = new Date().getTime().toString() + rand.toString();
    return id;
}

AjaxManager.prototype.implodeData = function(data){
    if (!data){
        return null;
    }
    var result = "";
    for (var key in data){
        result += "&" + key + "=" + data[key];
    }
    return result;
}


// WINDOW MANAGER =========================================================================================

var WindowsManager = function(){
    // const
    this.id         = "";
    // vars
    this.type       = "";
    this.data       = new Object();
    this.opened     = false;
    this.window     = null;
    this.window_content = null;
    this.openedPopups = [];
}

WindowsManager.prototype.init = function(){
    this.window_content = $("<div/>", {
        class : "popup-window-content",
        id    : "popup_window_content"
    })
    this.window     = $("<div/>", {
        class : "popup-window",
        id    : "popup_window"
    })
        .append(this.window_content);
    this.window.hide();
    $('body').append(this.window);
}

WindowsManager.prototype.open = function(type, input_data){
    if (!type){
        return;
    }
    this.data = input_data;
    if (type == "local"){
        var popup_window = $("#" + input_data.id);
        this.showWindow(null, popup_window);
    }else{
        if (typeof input_data !== "string"){
            data = input_data;
        }else{
            var data = {
                data : input_data
            }
        }
        data.type = type;
        data.label = window.location.pathname;
        this.opened = true;
        this.window.show();
        this.window_content.html("Loading...");
        ajaxManager.send("getPopupWindowContent", this.ajaxCallback, data, true);
    }
}

WindowsManager.prototype.showWindow = function(data, $window){
    if ($window){
        windowsManager.openedPopups.push($window);
        console.log(" button cancel length = " + $window.show().find("button[name='cancel']").length);
        $window.show().find("button[name='cancel']").click(windowsManager.close);
    }else{
        windowsManager.window_content.html(data).find("button[name='cancel']").click(windowsManager.close);
        windowsManager.window_content.find(".content").css({
            "width" : $(window).width() * 0.9 + "px",
            "height" : $(window).height() * 0.8 + "px",
            "overflow" : "scroll"
        });
    }
}

WindowsManager.prototype.close = function(){
    windowsManager.window.hide();
    windowsManager.window_content.html("");
    var openedPopups =  windowsManager.openedPopups;
    for (var i = openedPopups.length-1; i >= 0; i--){
        openedPopups[i].find("button[name='cancel']").click(function(){});
        openedPopups[i].hide();
        windowsManager.openedPopups.splice(i,1);
    }
    return false;
}

WindowsManager.prototype.ajaxCallback = function(data){
    // this not working at callback
    if (!windowsManager.opened){
        windowsManager.close();
        return;
    }
    windowsManager.showWindow(data);
}

/*  ----------------------------------------------------------------- */
/* IMPLEMENTATION --------------------------------------------------- */
/*  ----------------------------------------------------------------- */

$(document).ready(function(){
    initLogoBlock();
    windowsManager.init();
    // dynamic a href
    $(".header-logo,.header-title-container").click(function(){
        window.location.href = "/";
    }).css({cursor: "pointer"});
    // dynamic a href - catalogue
    $(".category-item,.product-item,.navigation li,.article-list-item").click(function(){
        window.location.href = $(this).find("a").attr("href");
    }).css({cursor: "pointer"});
    //


    //
    $("div[spoiler='true']").spoiler({
                effect: "slide",
                title: true
            })
            .removeAttr("title"); // if not it will be displayed as hint
    $("div[checkbox='true']").checkbox(false);
    // so it won't submit
});

/*  ---------------------------------------------------------------------- */
/* PUBLIC PAGE methods --------------------------------------------------- */
/*  ---------------------------------------------------------------------- */

(function($) {
    $(function() {
        $('ul.tabs').on('click', 'li:not(.current)', function() {
            $(this).addClass('current').siblings().removeClass('current')
                .parents('div.tab-section').find('div.tab-box').eq($(this).index()).fadeIn(150).siblings('div.tab-box').hide();
        })
    })
})(jQuery)


/** Init logo link to index */
function initLogoBlock(){
    $(".logo-block").css({
        cursor: "pointer"
    });
    $(".logo-block").click( function(event){
        location.href = "/";
    } );
    $("#search_form").css({
        cursor: "default"
    });
}

/** Insert text from search-example to search input-field */
function insertSearchInput(){
    var text = $("#search_input_text").html();
    var search = $("#search_input");
    inputManager.insertIntoInput(search, text);
}

/** RegForm: init */
function initRegForm(){
    //  add Nonbotable Field
    $("#reg_form").append("<input type=\"hidden\" name=\"nonbotable\" value=\"1\" />");
    // hide captcha
    $("#captcha_block").hide();
    // disable standard submit
    $("#reg_form").submit( onRegSubmit );
}

/** show captcha block */
function showCaptcha(){
    // show captcha
    $("#captcha_block").show();
}

/** RegForm: submit callback
 * @param data - ajax response
 */
function regSubmitCallback(data){
    if (data.result){
        //config Reg Form
        alert("regSubmitCallback():\n" + "User registered SUCCESSFULLY!")
    }
    else
    {
        if (data.show_captcha){
            console.log("regSubmitCallback():\n" + data.debug);
            showCaptcha();
            data.errors_key["keystring"] = true;
        }
        renderErrors($("#reg_errors"), data.errors);
        inputManager.setInputErrors(data.errors_key);
    }
}


/** Show recieved errors while reg process */
function renderErrors($block, errors){
    if (!$block){
        console.log("Error occurred while rendering errors!");
        return;
    }
    var html = "";
    for(var i = 0; i < errors.length; i++){
        // if already formatted?
        if (errors[i].substr(0,1) == "<"){
            html += errors[i] + "<br />";
        }else{
            html += "<span>Error: " + errors[i] + "</span><br />";
        }
    }

    $block.html(html)
          .find("span")
          .addClass("error-block");
}

/** Show recieved warnings while reg process */
function renderWarnings(block_id, warnings){
    var html = "";
    for(var i = 0; i < warnings.length; i++){
        // if already formatted?
        if (warnings[i].substr(0,1) == "<"){
            html += warnings[i] + "<br />";
        }else{
            html += "<span>Warning: " + warnings[i] + "</span><br />";
        }
    }

    $("#"+block_id)
            .html(html)
            .find("span")
            .addClass("warning-block");

}

/** RegForm: Click submit bitton */
function onRegSubmit(){
    var data = $("#reg_form").serialize();
    ajaxManager.send("regSubmit", regSubmitCallback, data);
    return false;
}

/** Changes captcha (ajax) */
function changeCaptcha(){
    var src = $("#captcha_image").attr("src");
    var real_src = $("#captcha_image").attr("real_src");
    if (!real_src){
        $("#captcha_image").attr("real_src", src);
        real_src = src;
    }
    var rand = Math.floor(Math.random() * 100000);
    $("#captcha_image").attr("src", real_src + "&" + rand);
}

function popupWindow(type, data){
    windowsManager.open(type, data);
}


/*  ---------------------------------------------------------------------- */
/* INSTANTIATION --------------------------------------------------------- */
/*  ---------------------------------------------------------------------- */


_debug = false;
inputManager = new InputManager();
ajaxManager = new AjaxManager("/services/service.ajax.php");
windowsManager = new WindowsManager();