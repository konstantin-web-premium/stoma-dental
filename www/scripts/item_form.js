var currentEditForm;
var callbackLoadDataExternal;

function itemForm_init($form){
    currentEditForm = $form;
    if (window["CKEDITOR"]){
        CKEDITOR.disableAutoInline = true;
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.contentsCss = '/styles/styles.css';
    }

    // PARENT change
    currentEditForm.find("#button_parent_change").click(function(){
        var popup_data = {
            id : "popup_parent_change"
        };
        windowsManager.open("local", popup_data);
    })
    currentEditForm.find("button[name='apply_parent']").click(function(event){
        var $selected = currentEditForm.find("select[name='select_parent_popup'] :selected");
        itemForm_setParent($selected);
        windowsManager.close();
    });
    //

    // TYPE change
    currentEditForm.find("#button_type_change").click(function(){
        var popup_data = {
            id : "popup_type_change"
        };
        windowsManager.open("local", popup_data);
    })
    currentEditForm.find("button[name='apply_type']").click(function(event){
        var $selected = currentEditForm.find("select[name='select_type_popup'] :selected");
        itemForm_setType($selected);
        windowsManager.close();
    });
    //

    // CURRENCY change
    currentEditForm.find("#button_currency_change").click(function(){
        var popup_data = {
            id : "popup_currency_change"
        };
        windowsManager.open("local", popup_data);
    })
    currentEditForm.find("button[name='apply_currency']").click(function(event){
        var $selected = currentEditForm.find("select[name='select_currency_popup'] :selected");
        itemForm_setCurrency($selected);
        windowsManager.close();
    });
    //

    // BRAND change
    currentEditForm.find("#button_brand_change").click(function(){
        var popup_data = {
            id : "popup_brand_change"
        };
        windowsManager.open("local", popup_data);
    })
    currentEditForm.find("button[name='apply_brand']").click(function(event){
        var $selected = currentEditForm.find("select[name='select_brand_popup'] :selected");
        itemForm_setBrand($selected);
        windowsManager.close();
    });
    //
    var ck = currentEditForm.find("textarea[name='content']").ckeditor()
        .closest(".input-block").css({width: "95%"});
}

function itemForm_setParent($option){
    var text = $option.text();
    var value = $option.val();
    currentEditForm.find("span[name='parent_id']").html(text);
    currentEditForm.find("input[name='parent_id']").val(value);
}

function itemForm_setType($option){
    var text = $option.text();
    var value = $option.val();
    currentEditForm.find("span[name='item_type']").html(text);
    currentEditForm.find("input[name='item_type']").val(value);
    var type = (value == 1 ? "category" : "product");
    itemForm_switchView(type);
    if (typeof(previewItem_init) === "function"){
        previewItem_init(type);
    }
}

function itemForm_setCurrency($option){
    var text = $option.text();
    var value = $option.val();
    currentEditForm.find("span[name='currency_id']").html(text);
    currentEditForm.find("input[name='currency_id']").val(value);
}

function itemForm_setBrand($option){
    var text = $option.text();
    var value = $option.val();
    currentEditForm.find("span[name='brand_id']").html(text);
    currentEditForm.find("input[name='brand_id']").val(value);
}


function setRobots(type){
    var val = "";
    switch(type){
        default:
        case 1:
            val = "index,follow";
            break;
        case 2:
            val = "noindex,follow";
            break;
        case 3:
            val = "index,nofollow";
            break;
        case 4:
            val = "noindex,nofollow";
            break;
    }
    currentEditForm.find("input[name='robots']").val(val);
}

/**
 * Send data to service
 * @param callback
 */
function itemForm_submitForm(callback){
    // hide errors
    itemForm_clearErrors(currentEditForm);
    var data = itemForm_serializeForm(currentEditForm);
    callbackLoadDataExternal = callback;
    ajaxManager.send("itemEditSubmit", loadDataCallback, data, false);
}

/**
 * Ajax callback (itemForm_submitForm)
 */
function itemForm_editData(data){
    if (!data.hasOwnProperty("result") || !data){
        alert("itemForm > itemForm_editData():\n" + data);
    }

    if (data.result){
        // TODO popup message
        alert("Page data updated! (" + data.result + ")");
        if (data.id){
            currentEditForm.find("input[name='id']").val(data.id);
        }
        itemForm_fillData(data.pageData);
    }
    else
    {
        renderErrors(currentEditForm.find("[name='errors_field']"), data.errors);
        //setInputErrors(data.errors_key);
    }
}


/**
 * Serialize (stringify) form data
 * @param $form
 * @returns {*}
 */
function itemForm_serializeForm(){
    var data = currentEditForm.serialize();
    return data;
}

/**
 * Clears error field
 * @param error_field_id
 */
function itemForm_clearErrors(){
    currentEditForm.find("[name='errors_field']").html("");
}

/**
 * Load ITEM
 * @param label
 */
function itemForm_load(id, type, callback){
    data = {
        id : (typeof id === "number" ? id : null),
        label : (typeof id === "string" ? id : null),
        edit_type : type
    };
    callbackLoadDataExternal = callback;
    ajaxManager.send("loadPageData", loadDataCallback, data, false);
}


/**
 * HANDLER loadItem
 * @param data
 */
function itemForm_fillData(data){
    currentEditForm.find("input[name='img_small'],input[name='img_medium'],input[name='img_large']").val("");
    var $editor = currentEditForm.find("textarea[name='content']");
//    $("button [name='button_delete_page']").removeAttr("disabled");
    itemForm_setLabelAndTitle(data["label"], data["title"]);
    currentEditForm.show();
    itemForm_clearErrors();

    for (var key in data){
        // pass numeric fetched items
        if (!isNaN(parseInt(key))){
            continue;
        }
        switch(key){
            default:
                findAndInput(key, data[key]);
                break;
            case "og":
                for(var og_key in data[key]){
                    findAndInput(key + "_" + og_key, data[key][og_key]);
                }
                break;
            case "blocks_id":
                var arr_id = data[key].split(",");
                var $blocks = currentEditForm.find("[checkbox='true'][name^='block_']");
                $blocks.checkbox(false);
                for(i = 0; i < arr_id.length; i++){
                    $form.find("[checkbox='true'][name='block_" + arr_id[i] + "']").checkbox(true);
                }
                break;
            case "content":
                $editor.val(data[key]);
                break;
            case "parent_id":
                var id = (data[key]!==null ? data[key] : 0);
                var $option = currentEditForm.find("select[name='select_parent_popup'] [value='" + id + "']");
                itemForm_setParent($option);
                break;
            case "type":
                var $option = currentEditForm.find("select[name='select_type_popup'] [value='" + data[key] + "']");
                itemForm_setType($option);
                switch($option.val().toString()){
                    case "1":
                        itemForm_switchView("category");
                        break;
                    case "2":
                    case "3":
                        itemForm_switchView("product");
                        break;
                }
                break;
            case "currency_id":
                var $option = currentEditForm.find("select[name='select_currency_popup'] [value='" + data[key] + "']");
                itemForm_setCurrency($option);
                break;
            case "brand_id":
                var $option = currentEditForm.find("select[name='select_brand_popup'] [value='" + data[key] + "']");
                itemForm_setBrand($option);
                break;
            case "user_edited_data":
                $(".user-data-block").html(data[key]);
                break;
            case "hidden":
                var value = (data[key] == "true" || data[key] == "1" ? true : false);
                $("div[name='hidden'][checkbox='true']").checkbox(value);
                break;
            case "img_small":
            case "img_medium":
            case "img_large":
                currentEditForm.find("input[type='hidden'][name='" + key + "']").val(data[key]);
                break;
        }
    }
}

function itemForm_switchView(type){
    var product_elements = currentEditForm.find("input[name='props'],input[name='price'],input[name='original_marking'],input[name='brand_id']" ).closest("div");
    var category_elements = currentEditForm.find("input[name='children_id']" ).closest("div");
    switch(type){
        default:
        case "category":
            product_elements.hide();
            category_elements.show();
            break;
        case "product":
            product_elements.show();
            category_elements.hide();
            break;
    }
}


function findAndInput(fieldName, value){
    var $fields = currentEditForm.find("[name='" + fieldName + "']").val(value)//.inputHints();
}

/**
 * Set label and title
 * @param $form
 * @param label
 * @param title
 */
function itemForm_setLabelAndTitle(label, title){
    console.log(label + ">> " + title);
    currentEditForm.find("input[name='label']").val(label);
    currentEditForm.find("input[name='title']").val(title);
    var title =
        "<a target=\"_blank\" href=\"/" + label + "\">" +
            title +
            "</a>" +
            "<br /><span class=\"grey-text\">[" +
            label +
            "]</span>";
    currentEditForm.find("[name='form_title']").html(title);
}


/**
 * Clean form
 * @param type
 */
function itemForm_empty(){
    itemForm_clearErrors();
    currentEditForm.show();
    //    $("#button_delete_page").attr("disabled", "disabled");

    // clean inputs
    var $inputs = currentEditForm.find("[type='text']");
    for (var i = 0; i<$inputs.length; i++){
        inputManager.clear($($inputs[i]));
    }
    // set defaults
    currentEditForm.find("input[name='id']").val(0);
    currentEditForm.find("input[name='img_small'],input[name='img_medium'],input[name='img_large']").val("");
    currentEditForm.find("textarea[name='content']").val("");
    var $option = currentEditForm.find("select[name='select_type_popup'] :first");
    itemForm_setType($option);
    var $option = currentEditForm.find("select[name='select_parent_popup'] :first");
    itemForm_setParent($option);
    $option = currentEditForm.find("select[name='select_brand_popup'] :first");
    itemForm_setBrand($option);
    $option = currentEditForm.find("select[name='select_currency_popup'] :first");
    itemForm_setCurrency($option);

    def_label = "label";
    def_title = "New Title";
    itemForm_setLabelAndTitle(def_label, def_title);
    currentEditForm.find("select[name='access']").val("4");
    $("div[name='hidden'][checkbox='true']").checkbox(true);
}

/**
 * AJAX delete page
 */
function itemForm_delete(callback){
    var data = {
        id : currentEditForm.find("input[name='label']").val()
    };
    ajaxManager.send("pageDelete", callback, data);
}

/******************************************************************/
/* PRIVATE CALLBACKS*****************************************************************/
/******************************************************************/
function loadDataCallback(data){
    if (!data || !data.hasOwnProperty("result")){
        var str = data;
        if (data){
            str = data + "\n";
            if (typeof data != "string"){
                for (var key in data){
                    str += "[" + key + "] = " + data[key] + "\n";
                }
            }
        }
        console.log("item_form::loadDataCallback >> Error:Wrong data format or HTML!\n" + str);
        return;
    }

    if (data.result){
        if (data.id){
            currentEditForm.find("input[name='id']").val(data.id);
        }

        itemForm_fillData(data);

        if (callbackLoadDataExternal){
            callbackLoadDataExternal(data);
        }
        callbackLoadDataExternal = null;
    }
    else
    {
        console.log("result = " + data.result + "!!!!!");
        renderErrors(currentEditForm.find("[name='errors_field']"), data.errors);
    }
}