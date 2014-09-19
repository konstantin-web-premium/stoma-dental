jQuery.fn.spoiler = function(userspoiler) {
    var options = {
        setclass : '',
        title : false,
        onlink : false,
        effect : "none",
        srcid : null
    }
    jQuery.extend(options, userspoiler);
    var title = "", text = "", css = false, user_class = "";
    getTitle = function(obj) {
        if (options.srcid !== null) {
            title = obj.text();
        } else {
            if (!options.title) {
                title = obj.children("span").text();
            } else {
                title = obj.attr("title");
            }
        }
        return title;
    }
    getText = function(obj) {
        if (!options.onlink) {
            text = obj.text();
        } else {
            text = obj.attr("spoiler");
        }
        return text;
    }
    chekTitle = function(title, text) {
        if (title == "") {
            text = jQuery.trim(text);
            var words = text.split(" ");
            title = words[0];
            var i = 1;
            while (title.length < 6) {
                title += " " + words[i];
                i++;
            }
            title += " ...";
        }
        return title;
    }
    if (!jQuery("#spoiler-style").data("isset")) {
        jQuery("head").append(jQuery('<style/>', {
            id : "spoiler-style",
            'class' : 'sp-link',
            text : ".spoiler {display: none;} .sp-link {cursor: pointer;}"
        }).data("isset", 1));
    }

    return this.each(function() {
        obj = jQuery(this);
        text = getText(obj);
        title = chekTitle(getTitle(obj), text);

        if (obj.data("srcid"))
            options.srcid = obj.data("srcid");

        if (options.setclass)
            user_class = " " + options.setclass;

        if (options.onlink) {
            obj.after(jQuery('<div/>', {
                    'class' : 'spoiler ' + obj.attr("class"),
                    text : obj.attr("spoiler")
                }).data("effect", options.effect)).replaceWith(jQuery('<span/>', {
                    'class' : 'sp-link' + user_class,
                    text : title
                }));
        } else {
            if (options.srcid !== null) {
                jQuery(options.srcid).hide().data("effect", options.effect);
                obj.data("srcid", options.srcid).addClass("sp-link").addClass(user_class);
            } else
                obj.addClass("spoiler").data("effect", options.effect).before(jQuery('<span/>', {
                    'class' : 'sp-link',
                    text : title
                }));

        }
    });
};

jQuery(document).on("click", ".sp-link", function() {
    var $this = jQuery(this);
    var sp = ($this.data("srcid")) ? jQuery($this.data("srcid")) : $this.next(".spoiler");
    switch(sp.data("effect")) {
        case "none":
            sp.toggle();
            break;
        case "fade":
            sp.fadeToggle();
            break;
        case "slide":
            sp.slideToggle();
            break;
    }

    return false;
});