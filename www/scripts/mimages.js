// MCatalog module script
var currentTR = {};

$(document).ready(function(){
    // TABLE -------------------------------------------------
    var table = $('#table_images_list');
    initTable(table);
    //
    var $form = $("#copy_image_form");
    $form.submit(function(){
        var data = {
            img_filename : $form.find("input[name='img_filename']").val(),
            filename :  $form.find("input[name='filename']").val(),
            image_type :  $form.find("input[name='image_type']").val()
        }
        ajaxManager.send("copyImageTmp", copyImageCallback, data, false);
        return false;
    });

    $("#button_upload_image").click(function(){
        fileUploader_activate(getSettings());
    });
});

function getSettings(){
    var $form = $("#copy_image_form");
    var type = $form.find("input[name='image_type']").val();
    switch(type){
        case "pages":
            var settings = {
                width: 270,
                height: 180,
                aspectRatio: "auto"
            }
            break;
        case "c_small":
            var settings = {
                width: 40,
                height: 40,
                aspectRatio: 1
            }
            break;
        case "c_medium":
            var settings = {
                width: 270,
                height: 180,
                aspectRatio: 270 / 180
            }
            break;
        case "c_large":
            var settings = {
                width: 100,
                height: 100,
                aspectRatio: "auto"
            }
            break;
    }
    settings.type = type;
    settings.$input = $form.find("input[name='img_filename']");
    settings.$preview_image = $form.find("img[name='preview']");
    return settings;
}

function copyImageCallback(data){
    if (data.result){
        // TODO insert image
        console.log("Image copy SUCCESS!");
        resetCopyForm();
        if (data["tr"]){
            var $tr = $(data["tr"]);
            $("#table_images_list").find("tbody").prepend($tr);
            $tr.find("button[name='delete_image']").click(onButtonDeleteClick);
        }
    }else{
        renderErrors($("#copy_image_form").find("[name='errors_field']"), data.errors);
        console.log("Image copy FAILED!");
    }
}

function deleteImageCallback(data){
    if (data.result){
        if (currentTR && currentTR.length){
            currentTR.remove();
            currentTR = null;
        }
    }else{
        if (currentTR && currentTR.length){
            currentTR.children("td:eq(4)").text(data.errors.join("<br />"));
        }
    }
}

function resetCopyForm(){
    var $form = $("#copy_image_form");
    $form.find("img[name='preview']").attr("src", "/images/catalogue/medium/nophoto.jpg");
    $form.find("input[name='filename']").val("");
    $form.find("[name='errors_field']").text("");
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

    table.find("button[name='delete_image']").click(onButtonDeleteClick);

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
                //window.location.href = "admin/catalogue/product?id="+id;
                $form.show();
                itemForm_load(parseInt(id), "catalogue", loadItemCallBack);
                minimizeTable(true);
            }
        }
    });

    table.tableFilter(table_options);
    setRowCount(table);
}

function onButtonDeleteClick(event){
    var $form = $("#copy_image_form");
    var $tr = $(event.target).closest("tr");
    currentTR = $tr;
    var img_filename = $tr.children('td:eq(2)').attr("name");
    var data = {
        img_filename : img_filename,
        image_type : $form.find("input[name='image_type']").val()
    }
    ajaxManager.send("deleteImage", deleteImageCallback, data, false);
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

