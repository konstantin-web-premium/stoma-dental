jQuery.fn.checkbox = function(checked) {
    var checked = checked;

    setValue = function(obj, new_value){
        obj.data("checked", new_value);
        if(new_value){
            obj.addClass(obj.attr("disabled")=="disabled" ? "checkbox-selected-disabled" : "checkbox-selected");
        }else{
            obj.removeClass(obj.attr("disabled")=="disabled" ? "checkbox-selected-disabled" : "checkbox-selected");
        }

        $form = obj.data("closestForm");
        if ($form){
            var name = obj.attr("name");
            $input = $form.find("input[name='" + name + "']");
            $input.val(new_value);
        }
    }

    changeValue = function(obj){
        var value = !obj.data("checked");
        setValue(obj, value);
    }


    return this.each(function() {
        obj = jQuery(this);
        obj.addClass(obj.attr("disabled")=="disabled" ? "checkbox-link-disabled" : "checkbox-link");
        name = obj.attr("name");
        $form = obj.closest("form");
        if ($form){
            obj.data("closestForm", $form);
            var $inputs = $form.find("input[name='" + name + "']");
            if ($inputs.length == 0){
               $form.append("<input type=\"hidden\" name=\"" + name + "\" value=\"false\" />");
            }
        }
        setValue(obj, checked);
    });
};

jQuery(document).on("click", ".checkbox-link", function() {
    obj = jQuery(this);
    changeValue(obj);
    return false;
});