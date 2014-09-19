// UPLOADER ----------------------------------
var CROPPER;
var SETTINGS = {};

$(document).ready(function(){
    // CROPPER ---------------------------------
    var $image = $(".cropper");
    fileUploader_settings("c_meduim");
    $image.cropper({
        aspectRatio: 270 / 180,
        data: {x1: 0, y1: 50, width: 270, height: 180},
        preview: ".extra-preview",
        done: function(data) {
            //console.log(data);
        }
    });
    $image.cropper("enable");
    //

    var $form = $("form[class='file-uploader-form']");
    $form.submit(function(){
        return false;
    });
    $form.find('input[name="file_browse"]').ajaxfileupload({
        action: '/services/service.ajax.php',
        params: {
            action: "uploadImage",
            html: "false"
        },
        'onComplete': onCompleteHandler,
        'onStart': function() {
            $form.find("[name='upload_status']").html("Started");
        },
        'onCancel': function() {
            $form.find("[name='upload_status']").html("No file selected");
        }
    });
    $form.find("button[name='cropper_reset']").click( resetCropperHandler);
    $form.find("button[name='cropper_done']").click(function (){       // set reset button handler
        var $form = $("form[class='file-uploader-form']");
        var img_filename = $form.find('input[name="file_browse"]').attr("uploaded_filename");
        if (!img_filename || img_filename == ""){
            alert("Filename is undefined");
            return;
        }
        if (!CROPPER){
            alert("no cropper");
            return;
        }
        var data = CROPPER.cropper("getData");
        data.filename = img_filename;
        data.image_type = SETTINGS.type;
        //DEBUG
        console.log("sent to 'cropProductImage' with data: img_filename=" + data.filename + " image_type=" + SETTINGS.type);
        //

        ajaxManager.send("cropProductImage", cropImageCallback, data, false);
        CROPPER.cropper("disable");
        windowsManager.close();                            // show cropper
    });

    $("#button_delete_image").click(function(){
        fileUploader_settings("meduim");
        deleteProductImage(false);
    });

    $("#button_delete_image_large").click(function(){
        fileUploader_settings("large");
        deleteProductImage(false);
    });
});

/**
 * Activate file_browse and further cropper with settings
 * @param data
 */
function fileUploader_activate(settings){
    SETTINGS = settings;
    var $form = $("form[class='file-uploader-form']");
    $form.find("[name='file_browse']").click();
    console.log("fileUploader_activate > " + settings.type);
}


function onCompleteHandler(response){
    if (!response.data){
        $("#upload_errors").html("ERROR");
        return;
    }
    if (response.data.result){
        var $form = $("form[class='file-uploader-form']");
        var $this = $form.find('input[name="file_browse"]');
        $form.find("[name='upload_status']").html("");                      // clear status when successful
        var img_width = response.data.img_width;                            // store received width
        var img_height = response.data.img_height;                          // store received height
        var css = {
            width: img_width,
            height: img_height
        };
        $img = insertEmptyImg($form.find(".cropper-img"));
        $img.closest("div").css(css);                                       // set them as css
        var img_filename = response.data.img_filename;
        var img_url = "/images/catalogue/_tmp/" + img_filename;
        $this.attr("uploaded_filename", img_filename);
        $img.attr("src", img_url);                                         // change image

        $(".extra-preview").css({
            "width" : (SETTINGS.aspectRatio != "auto" ?SETTINGS.width+"px" : "auto"),
            "height" : (SETTINGS.aspectRatio != "auto" ?SETTINGS.height+"px" : "auto"),
            "max-width" : "400px",
            "max-height" : "400px"
        });

        var options = {                                                     // cropper options
            aspectRatio: SETTINGS.aspectRatio,
            data: {x1: 0, y1: 0, width: SETTINGS.width, height: SETTINGS.height},
            preview: ".extra-preview",
            done: function(data) {                                          // DONE cropper handler
                $form.find("[class='cropper-rendered-data']").html(data.width + " x " + data.height);
            }
        };
        setImageCropper($img, options);                                     // set cropper
        //$form.find(".cropper-container").show();                            // show cropper
        var popup_data = {
            id : "popup_image_uploader"
        };
        windowsManager.open("local", popup_data);
    }else{
        CROPPER.cropper("enable");
        $form.find("[name='upload_status']").html("Failed:");
    }
    var errors = response.data.errors;
    var warnings = response.data.warnings;
    renderErrors($("#upload_errors"), errors );
    renderWarnings("upload_warnings", warnings );
}

function resetCropperHandler(){
    if (CROPPER){
        CROPPER.cropper("setData", {width: SETTINGS.width, height: SETTINGS.height});             // reset cropper
        CROPPER.cropper("enable");
    }
}

/**
 * value (bool) -
 * @param value
 */
function deleteProductImage(value){
    /*
    value = (value && $("#button_delete_image").attr("closed") == "true");!!!
    var $button = $(SETTINGS.button_delete_image);
    var $img = $("li.product-item img");
    if (!$img.attr("img_filename")){
        return;
    }
    if (value){
        $button.text("Restore")
            .attr("closed", "true")
            .closest("div")
            .css({position : "inherit"});
        $img.attr("src", "/images/catalogue/medium/nophoto.jpg");
        $("#product_edit_form input[name='img_filename" + suffix + "']").val("");
    }else{
        $button.text("X").attr("closed", "false").closest("div").css({position : "relative"});
        $img.attr("src", "/images/catalogue/_tmp/" + $img.attr("img_filename"));
        $("#product_edit_form input[name='img_filename" + suffux + "']").val($img.attr("img_filename" + siffix));
    }
    */
}

function insertEmptyImg($into, attrs){
    if (!attrs){
        attrs = {};
    }
    $into.children().remove();
    var $img = $("<img />", attrs);
    $into.append($img);
    return $img;
}

function cropImageCallback(data){
    //DEBUG
    if (!data){
        console.log("file_uploader::cropImageCallback() >> Data is undefined");
    }else{
        console.log("file_uploader::cropImageCallback() >> result:" + data.result + "; img_filename = " + data["img_filename"]);
    }
    //

    if (data.result){
        var img_url = "/images/catalogue/_tmp/" + data["img_filename"];
        if (SETTINGS.$input && SETTINGS.$input.length){
            SETTINGS.$input.val(data["img_filename"]);
        }
        if (SETTINGS.$preview_image && SETTINGS.$preview_image.length){
            SETTINGS.$preview_image.attr("src", img_url);
        }
    }else{
        alert("FAIL crop");
    }
}

function fileUploader_settings(type){
    switch(type){
        default:
        case "meduim":
            SETTINGS = {
                width: 270,
                height: 180,
                aspectRatio: 270 / 180,
                form_img_var_name : "input_img_filename",
                $preview_image : $(".image_medium_preview").find("img[name='image']"),
                type : "medium"
            }
            break;
        case "c_meduim":
            SETTINGS = {
                width: 270,
                height: 180,
                aspectRatio: 270 / 180,
                $input : "input_img_filename",
                $preview_image : $(".image_medium_preview").find("img[name='image']"),
                type : "c_medium"
            }
            break;
        case "large":
            SETTINGS = {
                width: 500,
                height: 500,
                aspectRatio: "auto",
                form_img_var_name : "input_img_large_filename",
                $preview_image : $(".image_large_preview").find("img[name='image']"),
                type : "large"
            }
            break;
    }
}

function setImageCropper($img, options){
    CROPPER = $img;
    CROPPER.cropper(options);
    CROPPER.cropper("enable");
}