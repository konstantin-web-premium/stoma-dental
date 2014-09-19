// jQuery Input Hints plugin
// Copyright (c) Rob Volk
// https://github.com/robvolk/jQuery.InputHints
// http://robvolk.com/jquery-form-input-hints-plugin

(function ($) { // alias the $ function for use with jquery in no-conflict mode
    $.fn.inputHints = function () {
        function showHints(el) {
            if (!el.attr){
                el = $(el);
            }
            if (el.attr("type") == "hidden"){
                return;
            }

            el.addClass('input-inited');
            if (el.val() == ''){
                var error = (el.attr("error") == "1" || el.attr("error") == "true");
                var addon_class = (error ? "input-error" : "input-hinted");
                el.val(el.attr('hint'))
                    .addClass(addon_class)
                    .attr("inited", "false");
            }else{
                el.attr("inited", "true");
            }
        };

        function hideHints(el) {
            if (!el.attr){
                el = $(el);
            }
            if (el.attr("type") == "hidden"){
                return;
            }

            if (el.attr('inited') != "true"){
                el.val('')
                    .removeClass('input-hinted')
                    .removeClass('input-error')
                    .attr("inited", "true");
            }
        };

        // hides the input display text stored in the placeholder on focus
        // and sets it on blur if the user hasn't changed it.

        var el = $(this);
        el.removeClass('input-hinted')
            .removeClass('input-error');

        // show the display text on empty elements
        el.each(function () {
            //showHints(this);
        });

        // hook up the blur &amp; focus
        return el.focus(function () {
            //hideHints(this);
        }).blur(function () {
            //showHints(this);
        });
        /*.on("keypress", function(event){
            showHints(this);
        })
        */
    };
})(jQuery);