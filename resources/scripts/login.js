// Global Variable
tullok = {};

$(function () {
    $("label.over").each(function () {
        var id = $(this).attr("for"); // Id of target element
        var label = $(this); // Current label. $(this) will change in event handlers
        var target = $("#" + id); // Target element of label

        if (target.attr("value")) {
            $(this).hide();
        } else {
			$(this).show();
		}

        target.focus(function () {
		label.hide();
        }).blur(function () {
            if (!$(this).attr("value")) { // Make sure there isn't text there
                label.show();
            }
        }).change(function () {
            if ($(this).attr("value")) { // Make sure there isn't text there
                label.hide();
            } else {
                label.show();
            }
        });
    });
});
