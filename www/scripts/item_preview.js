var previewItem;

$(document).ready(function(){
    $(".category-item").hide();
    $(".product-item").hide();
    $(".small-preview").hide();
    $(".large-preview").hide();
    $("form input[name='title']").on("change", function(event){
        previewItem_setTitle($(event.target).val());
    })
});

function previewItem_init(type){
    $(".product-preview-image-buttons").show();
    $(".category-item").hide();
    $(".product-item").hide();
    if (type == "category"){
        previewItem = $(".category-item").show();
        $(".small-preview").show();
        $(".large-preview").hide();
    }else{
        previewItem = $(".product-item").show();
        $(".small-preview").hide();
        $(".large-preview").show();
    }
    previewItem = $(".category-item,.product-item");
}

function previewItem_setPrice(native, foreign, amount){
    var $a_buy = previewItem.find("a.product-buy").hide();
    var $a_order = previewItem.find("a.product-order").hide();
    var $a_request = previewItem.find("a.product-request-price").hide();

    if (native != 0){
        previewItem.find(".price-native").html(native + "&nbsp;UAH");
        previewItem.find(".price-foreign").html(foreign + "&nbsp;$");
        previewItem.find(".price-block").show();
        if(amount == 0){
            $a_order.show();
        }else{
            $a_buy.show();
        }
    }else{
        previewItem.find(".price-block").hide();
        $a_request.show();
    }

}

function previewItem_setDefaultImages(){
    var src = "/images/catalogue/medium/nophoto.jpg";
    previewItem.find("img[name='image_preview']").attr("src", src);
    src = "/images/catalogue/large/nophoto.jpg";
    $(".large-preview").find("img[name='image_preview']").attr("src", src);
}

function previewItem_setSmallImage(src){
    console.log("Try to set small as "+src);

    if(!src || src == ""){
        src = "/images/catalogue/small/nophoto.jpg";
    }
    $(".small-preview").find("img[name='image_preview']").attr("src", src);
}

function previewItem_setMediumImage(src){
    console.log("Try to set medium as "+src);

    if(!src || src == ""){
        src = "/images/catalogue/medium/nophoto.jpg";
    }
    previewItem.find("img[name='image_preview']").attr("src", src);
}

function previewItem_setLargeImage(src){
    if(!src || src == ""){
        src = "/images/catalogue/large/nophoto.jpg";
    }
    $(".large-preview").find("img[name='image_preview']").attr("src", src);
}

function previewItem_setTitle(title){
    previewItem.find("a[name='title']").html(title);
}

function previewItem_setBrand(brand_str, brand_img_url){
    if (!brand_str){
        brand_str = "";
    }
    if (brand_img_url){
        previewItem.find("img.brand-logo").show().attr("src", brand_img_url);
    }else{
        previewItem.find("img.brand-logo").hide();
    }
    previewItem.find(".country").html(brand_str);
}

function previewItem_setOptions(arr){
    previewItem.find(".description").hide();
    var $table = previewItem.find(".options-table").show();
    $table.find("tr").remove();
    for (var i = 0; i < arr.length; i++){
        var $tr = $("<tr />");
        var $td1 = $("<td />", {
            html : "prop title"
        });
        var $td2 = $("<td />", {
            html  : "rendered",
            width : "30%",
            align : "right"
        });
        $tr.append($td1, $td2);
        $table.append($tr);
    }
}

function previewItem_setDescription(html){
    previewItem.find(".description").show().html(html);
    previewItem.find(".options-table").hide();
}

function previewItem_new(){
    previewItem_setPrice(0,0,0);
    previewItem_setDefaultImages();
    previewItem_setTitle("New Title");
    previewItem_setDescription("");
    previewItem_setBrand("","");
}
