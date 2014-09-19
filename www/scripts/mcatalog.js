// MCatalog module script
var IMAGES_SETTINGS = {};

$(document).ready(function(){
    $("#categories_tree").html("Loading...");
    ajaxManager.send("getCategoriesTree", getCategoriesCallback, null, true);

    // ITEM FORM -------------------------------------------------------
    var $form = $("#product_edit_form");
    itemForm_init($form);

    $("#button_change_image_small").click(function(){
        IMAGES_SETTINGS = {
            $input_image : $form.find("input[name='img_small']"),
            $image_preview : $(".small-preview").find("img[name='image_preview']"),
            img_path : "/images/catalogue/small/"
        };
        popupWindow('getImagesTable', 'c_small');
    });

    $("#button_change_image_medium").click(function(){
        IMAGES_SETTINGS = {
            $input_image : $form.find("input[name='img_medium']"),
            $image_preview : $(".category-item,.product-item").find("img[name='image_preview']"),
            img_path : "/images/catalogue/medium/"
        };
        popupWindow('getImagesTable', 'c_medium');
    });

    $("#button_change_image_large").click(function(){
        IMAGES_SETTINGS = {
            $input_image : $form.find("input[name='img_large']"),
            $image_preview : $(".large-preview").find("img[name='image_preview']"),
            img_path : "/images/catalogue/large/"
        };
        popupWindow('getImagesTable', 'c_large');
    });

    $("#button_create_page").click( function (event){
        itemForm_empty();
    });

    $("#button_delete_page").click( function (event){
        itemForm_delete(deletePageCallback);
    });

    $form.find("select[name='item_type']").change(function(event){
        itemForm_switchView($(event.target).val() == "1" ? "category" : "product");
    });

    $form.hide()
        .submit( function(){
            itemForm_submitForm(editItemCallBack);
            return false;
        })
        .find("[type='reset']").click( function(event){
            var id = $("#select_page").val();
            if (id != "0"){
                itemForm_load(id, loadItemCallBack);
            }
            return false;
        });
    //

    // TABLE -------------------------------------------------
    var table = $('#table_products_list');
   initTable(table);
    //


});

/**
 * this method call from popup as <a href='javascript:... />
 */
function onImageSelected(img_filename){
    console.log("onImageSelected :: " + img_filename);
    var img_file = IMAGES_SETTINGS.img_path + img_filename + ".jpg";
    console.log(IMAGES_SETTINGS.img_path +"|||" + img_filename );
    if (IMAGES_SETTINGS.$input_image){
        IMAGES_SETTINGS.$input_image.val(img_filename);
    }
    if (IMAGES_SETTINGS.$image_preview){
        IMAGES_SETTINGS.$image_preview.attr("src", img_file);
    }
    windowsManager.close();
}


/**
 * Updates TABLE DATA
 * @param action
 * @param data
 */
function updateProductsTable(action, data){
    if (!data){
        return;
    }
    var $tr = $(data);
    var id = $tr.children('td:eq(0)').text();
    var $table = $('#table_products_list');
    currentTR = $table.find("tr[name='" + id + "']");
    if (currentTR.length == 0){
        console.log("new TR name = "+ $tr.attr("name"));
        /*
        var id = $tr.children('td:eq(0)').text();
        $tr.attr(name=id)
        */
        $table.find("tbody").prepend($tr);
        currentTR = $tr;
    }else{
        var $prev_tr = currentTR.prev("tr");
        if ($prev_tr.length == 0){
            var $next_tr = currentTR.next("tr");
        }
        console.log("prev len = " + $prev_tr.length);
        currentTR.remove();
        if ($prev_tr.length > 0){
            console.log("insert TR after " + $prev_tr);
            $prev_tr.after($tr);
        }else if ($next_tr.length > 0){
            console.log("insert TR before " + $next_tr);
            $next_tr.before($tr);
        }else{
            console.log("insert TR in TBODY");
            $table.find("tbody").prepend($tr);
        }
        currentTR = $tr;
    }
}


function initTable(table){
    var table_options = {
        clearFiltersControls: [$('#table_clean_filters')],
        filteringRows: function(filterStates) {
            table.addClass('filtering');
        },
        filteredRows: function(filterStates) {
            table.removeClass('filtering');
            setRowCount(table);
        }
    };

    $("#button_minimize_table").click(function (){
        minimizeTable( $(".semi-spoiler").attr("minimized") != "true" );
    });

    $("#button_create_new").click(function (){
        minimizeTable( true );
        itemForm_empty();
        previewItem_init("product");
        previewItem_new();
    });

    table.click(function (event){
        if (event.target.toString().indexOf("HTMLTableCellElement") > -1){
            var tr = $(event.target).closest("tr");
            var id = tr.attr("name") | 0;
            if (id > 0){
                //window.location.href = "/admin/catalogue/product?id="+id;
                $form.show();
                itemForm_load(parseInt(id), "catalogue", loadItemCallBack);
                minimizeTable(true);
            }
        }
    });

    table.tableFilter(table_options);
    setRowCount(table);
}

function editItemCallBack(data){
    // # TODO "success" popup
    alert("Data updated SUCCESSFULLY!");
    loadItemCallBack(data);
    console.log(data.result + " " + data.price_uah + " " + data.tr_data);
    updateProductsTable("edit", data["tr_data"]);
}


function loadItemCallBack(data){
    //itemForm_fillData(data);
    previewItem_init((data.type == "1") ? "category" : "product" );
    previewItem_setPrice(data.price_uah, data.price_usd, data.amount);
    previewItem_setTitle(data.title);
    previewItem_setDescription(data.description);
    // images
    var img_small_url = (data.img_small ? "/images/catalogue/small/" + data.img_small + ".jpg" : "");
    var img_medium_url = (data.img_medium ? "/images/catalogue/medium/" + data.img_medium + ".jpg" : "");
    var img_large_url = (data.img_large ? "/images/catalogue/large/" + data.img_large + ".jpg" : "");
    previewItem_setSmallImage(img_small_url);
    previewItem_setMediumImage(img_medium_url);
    previewItem_setLargeImage(img_large_url);
    previewItem_setBrand(data.brand_data, data.brand_img_url);
}

function minimizeTable(value){
    var css = {height : "auto"};
    if (value){
        $(".semi-spoiler").attr("minimized", "true");
        $("#button_minimize_table").html("&nbsp;&#8595;&nbsp;");
        css ={
            height : $(".semi-spoiler").attr("height") + "px"
        };
    }else{
        $(".semi-spoiler").removeAttr("minimized");
        $("#button_minimize_table").html("&nbsp;_&nbsp;");
    }
    $(".semi-spoiler").css(css);
}

function setRowCount(table) {
    var rowcount = table.find('tbody tr:not(:hidden)').length;
    $('#table_rowcount').text('Items: ' + rowcount);
}


function getCategoriesCallback(data){
    $("#categories_tree").html(data);
    $("#categories_tree").find(".spoilers-tree").spoiler({
        effect: "slide",
        title: true
    })
        .removeAttr("title"); // if not it will be displayed as hint;

}


