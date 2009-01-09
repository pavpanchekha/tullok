// Pavel Panchekha

function update() {
    $.getJSON("databases/json/all", function (data) {
        for (var i = 0; i < data.length; i++) {
             data[i]["numTables"] = data[i]["numTables"].toString();
             data[i]["url"] = "databases/browse/" + data[i]["name"];
        }
         
        $(".scrolling ul").eq(0).items("replace", data);
    });
}

$(function () {
    $(".selectitem").dblclick(function () {
        $(this).find("a").click();
        return false;
    }).find("a").click(function () {
        alert("Dummy Function: Going to browse " + $(this).parent().find("h2").text());
        // TODO: Replace
        return false;
    });

    $("a[@rel=delForm-show]").click(function () {
        var db = $(".selectitem.selected");
        var dbName = $(".selectitem.selected").find("h2").text();

        if (db.length > 0) {
            tb_show("Delete Database", "databases/delete?dbName=" + dbName +
            "&thickbox&TB_iframe=true&height=150&width=350&modal=true", false);
            $("#delForm").toggle();
            $(this).toggleClass("clicked");
            reheight();
        }
        
        return false;
    });

    $("#delForm").submit(function () {
        var dbName = $(this).find("input").attr("value");

        tb_show("Delete Database", "databases/delete/" + dbName + "?thickbox&TB_iframe=true&height=150&width=350&modal=true", false);
        $("a[@rel=delForm-show]").click();
        return false;
    });

    $.getJSON("databases/json/all", function (data) {
        for (var i = 0; i < data.length; i++) {
            data[i]["numTables"] = data[i]["numTables"].toString();
            data[i]["url"] = "databases/browse/" + data[i]["name"];
        }
        
        $(".scrolling ul").eq(0).items(data).chain({
            ".name": "{name}",
            ".numTables": "{numTables}",
            ".tables": "{tables}",
            ".url": {href: "{url}", content: "Browse"},
        });
    });
    
    $(".selectitem").livequery("click", function () {
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(".selectitem.selected").removeClass("selected");
            $(this).addClass("selected");
        }
    }).livequery("dblclick", function () {
        $(this).find("a").click();
    });

    $("#addForm form").submit(function () {
        var db = $(this).find("input").attr("value");

        if (!db) {
            db = "";
        }

        $.post("databases/json/add", {"dbName": db}, function (data) {
            if (data.Status == "OK") {
                tullok.console.status(data.Message, "tick", data.SQL, "sql");
                update()
            } else if (data.Status == "ERROR") {
                tullok.console.status(data.Message, "close", data.SQL, "sql");
            }
        }, "json");

	$("a[@rel=addForm-show]").click();
        return false;
    });

    // Start auto-updater
    tullok.update = setInterval(update, 10000);
});
