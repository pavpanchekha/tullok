// Global Variable
tullok = {};

function aToBarID(rel) {
    return "#" + rel.split("-")[0];
    // I'm assuming we don't use rel for anything else
}       

function barIDToA(id) {
    return $("#menu a[@rel=" + id + "-show]");
}

function reheight() {
    var h = tullok.oHeight;
    $(".bar:not(:hidden)").each(function () {
        h += $(this).height();
    });
    $(".content").css("top", h + "px");
}

// Set global variables
tullok.menuClicked = false;
tullok.statusText = "&copy; 2008 Tullok";
tullok.history = [];
tullok.historyPos = 0;
tullok.oHeight = 0;

$(function () {
    tullok.console = new Console($("#console"));
    
    if ($.tablesorter) {
	$.tablesorter.defaults.widgets = ['zebra'];
    }
    
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

    $(".selectitem").click(function () {
        if (!$(this).hasClass("selected")) {
            $(".selectitem.selected").removeClass("selected"); // Unselect others
        }

        $(this).toggleClass("selected"); // Select
    }).dblclick(function () {
        $(this).find("a").click(); // Click the link, so we don't need to attach more handlers
        return false;
    }).removeClass("selected");

    $("#menu a[@rel]").click(function (e) {
        var elem = $(this);
	var isms = $(this).hasClass("clicked");

	if (!isms) {
	    $("#menu a.clicked").removeClass("clicked");
	}

        $(".bar.second, .bar.third").not(aToBarID($(this).attr("rel"))).hide();
        $(aToBarID($(this).attr("rel"))).toggle().find("input").focus();
        $(this).toggleClass("clicked");
        reheight();

        return false;
    });

    $(".bar .close").click(function () {
        $(barIDToA($(this).parent().attr("id"))).click();
        return false;
    });

    $("#quicklinks").click(function () {
	tullok.menuClicked = true;
	$("#quicklinks > ul").toggle();
	$(this).toggleClass("selected");
    }).find("a").click(function () {
	tullok.menuClicked = true;
	$("#quicklinks > ul").toggle();
	$(this).parent().toggleClass("selected");
        return false;
    });

    $(document).click(function () {
        if (!tullok.menuClicked && $("#quicklinks").hasClass("selected")) {
            $("#quicklinks > ul").hide();
            $("#quicklinks").removeClass("selected");
        }

        tullok.menuClicked = false;
    });

    $.getJSON("databases/json/list", function (data) {
        for (var i = 0; i < data.length; i++) {
	    var name = data[i].name;
	    var num = data[i].numTables;
            var html = "<li class='database'>";
            html += "<img src='resources/images/plus.png' alt='+' />";
            html += "<a href='databases/browse/" + name + "'>";
            html += name;
            html += "<span class='num'> (";
            html += num;
            html += ") </span>";
            html += "</a>";
            html += "<ul />";
            html += "</li>";
            var elem = $(html);

            elem.find("img").toggle(function () {
                $(this).attr("src", "resources/images/minus.png");
                $(this).parent().find("ul").toggle();
                return false;
            }, function () {
                $(this).attr("src", "resources/images/plus.png");
                $(this).parent().find("ul").toggle();
                return false;
            });

            elem.find("img").click(function () {
        	if ($(this).parent().find("ul li").length) {
        	    return false;
        	}

                var elem = $(this).parent();
                $.getJSON("databases/json/tables", {"dbName": $(this).parent().text().split(" ")[0]}, function (data) {
                    for (var i = 0; i < data.length; i++) {
                        elem.find("ul").append("<li class='table'><a href='tables/browse/" + elem.parent().text().split(" ")[0] + ":" + data[i] +"'>" + data[i] + "</a></li>");
                    }
                    elem.find("ul").show();
                });
                return false;
            });

            $("#quicklinks > ul").append(elem);
        }
    });

    var t = setTimeout(function () {
        tullok.oHeight = $(".content").offset().top;
        $(".bar:not(:hidden)").each(function () {
            tullok.oHeight -= $(this).height();
        });
    }, 100);
});
