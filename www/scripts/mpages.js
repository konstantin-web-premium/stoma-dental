// MPages module script

$(document).ready(function(){
    var $form = $("#page_edit_form");
    itemForm_init($form);

    $("#button_create_page").click( function (event){
        itemForm_empty($form);
    });

    $("#button_delete_page").click( function (event){
        itemForm_delete($form, deletePageCallback);
    });

    $form.hide()
        .submit( function(){
            itemForm_submitForm($form, function(data){
                                            itemForm_editData($form, data);
                                            updatePageList($("select_page"), data.itemList, data.newPageData["label"]);
                                        }
            );
            return false;
        })
        .find("[type='reset']").click( function(event){
            var label = $("#select_page").val();
            if (label != "0"){
                itemForm_load(label);
            }
            return false;
        });

    // SELECT ---------------------------------------------------------------------------------------------
    $("#select_page").on("change", function(event){
        var $this = $(this);
        var label = $this.val();
        if (label != "0"){
            itemForm_load(label, "page", function(data){itemForm_fillData($form, data);} );
        }
    });
});

/** AJAX CALLBACK
    action -> "pageDelete"
 */
function deletePageCallback(data){
    if (data.result){
        alert("deletePageCallback()\n"+"Page deleted successfully");
    }else{
        renderErrors("edit_page_errors", data.errors);
    }
}

function updateItemList($select, itemList, label){
    $select.empty()
        .append( $("<option value='0'>-Select " + type + "-</option>"));
    for (var i = 0; i < itemList.length; i++){
        var option = itemList[i];
        $select.append( $("<option value='" + option["label"] + "'>(" + option["label"] + ") " + option["title"] + "</option>"));
    }
    $select.find("option[value='" + label + "']").attr("selected", "selected");
}