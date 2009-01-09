// Pavel Panchekha

function Console(jq) {
    this.main = jq;
    this.input = jq.find("input[@type='text']");

    $("#show-console").toggle(function () {
	tullok.statusText = $("#footer .status").html();
        $("#footer .status").html("Interactive Console");
	$("body").addClass("console");
	$("#show-console").attr({src: "resources/images/down.png", title: "Hide Error Console", alt: "Hide Error Console"});
    }, function () {
	$("#footer .status").html(tullok.statusText);
	$("body").removeClass("console");
	$("#show-console").attr({src: "resources/images/up.png", title: "Show Error Console", alt: "Show Error Console"});
    });

    $("#edit-mode").toggle(function () {
	$("body").addClass("editable");
	$("#edit-mode").attr({src: "resources/images/unlock.png", title: "No Edit Mode", alt: "No Edit Mode"});
        $.cookie("edit_mode", true);
    }, function () {
	$("body").removeClass("editable");
	$("#edit-mode").attr({src: "resources/images/lock.png", title: "Allow Edit Mode", alt: "Allow Edit Mode"});
        $.cookie("edit_mode", null);
    });

    if ($.cookie("edit_mode")) {
        $("#edit-mode").click();
    }

    var console = this;
    this.main.find("form").submit(function (e) {e.preventDefault(); return console.exec();});

    this.main.find("input.text").keypress(function (e) {
	if (e.keyCode == 40 && tullok.historyPos < tullok.history.length || e.keyCode == 38 && tullok.historyPos > 0) {
	    tullok.history[tullok.historyPos] = $("#console input.text").attr("value");
	    tullok.historyPos += e.keyCode == 40 ? 1 : -1; // Or, you know, an if-else
	    console.input.attr("value", tullok.history[tullok.historyPos]);
        }

	return true;
    });

    this.main.find("li .showCode").livequery("click", function () {
	$(this).parent().parent().find(".code").toggle();
    });

    this.main.find("li .showResults").livequery("click", function () {
	$(this).parent().parent().find(".results").toggle();
    });
}

Console.prototype.status = function (msg, img, code, lang, results) {
    if (img) msg = "<img src='resources/images/" + img + ".png' alt='' />" + msg;
    if (!$("body", self.top.document).hasClass("console")) $("#footer .status", self.top.document).html(msg);

    if (code) msg += "<img class='showCode' src='resources/images/code.png' alt='Show Code' title='Show Code' />";
    if (results) msg += "<img class='showResults' src='resources/images/results.png' alt='Show Results' title='Show Results' />";

    msg = "<p>" + msg + "</p>";

    if (code) msg += "<pre class='code'>" + code + "</pre>";
    if (results) msg += "<div class='results'>" + results + "</div>";

    msg = "<li>" + msg + "</li>";

    var t = $("#footer ul", self.top.document).prepend(msg);

    if (code) {
        t.find("li").eq(0).find(".showCode").click(function () {
            $(this).parent().parent().find(".code").toggle();
        });
    }

    if (results) {
        t.find("li").eq(0).find(".showResults").click(function () {
            $(this).parent().parent().find(".results").toggle();
        });
    }

    if (lang) {
	$("#console .code").eq(0).addClass("sh_" + lang);
	sh_highlightDocument();
    }
};

Console.prototype.exec = function () {
    // Take care of console submissions
    _this = this
    var cmd = this.input.attr("value");
    if (!cmd) return false;
    var cmdA = cmd.split(" ");

    tullok.history.push(cmd);
    tullok.historyPos++;

    switch (cmdA[0]) {
    case "js": case "javascript":
	var lang = "javascript";
	cmd = cmd.replace("js ", "").replace("javascript ", "");
	try {
	    var result = eval(cmd);
	    var text = "Javascript Executed Successfully";
	    var type = "tick";
	} catch(err) {
	    var result = err;
	    var text = "Error in Javascript Execution";
	    var type = "close";
	}
	break;

    case "databases": case "users": case "tables": case "backup":
	// TODO: Keep up to date
	var lang = "sh";
	var urlA = cmdA.splice(2, cmdA.length - 2);
	var url = "";
	var urlHead = "";
	for (var i = 0; i < urlA.length; i++) {
	    if (urlA[i].charAt(0) == "-") {
		if (i+1 < urlA.length && urlA[i+1].charAt(0) != "-" && urlA[i].charAt(1) == "-") {
		    url += urlA[i] + " " + urlA[i+1];
		    i++;
		} else {
		    url += urlA[i];
		}
		// Voodoo
	    } else {
		urlHead += urlA[i] + " ";
	    }
	}

	var match;
	var args = {};
	while ((match = / ?--?(\w+) (\w+)/g.exec(url)) != null) {
	    console.log(match);
	    args[match[1]] = match[2];
	    console.log(args);
	}

	url = url.replace(/ ?--?(\w+) (\w+)/g, "");

	while ((match = / ?--(\w+)=(\w+)/g.exec(url)) != null) {
	    args[match[1]] = match[2];
	}

	url = url.replace(/ ?--(\w+)=(\w+)/g, "");

	while ((match = / ?--?(\w+)/g.exec(url)) != null) {
	    args[match[1]] = undefined;
	}

	url = url.replace(/ ?--?(\w+)/g, "");

	urlHead = urlHead.replace(/(\S+) /, "$1/");
	urlHead = cmdA[0] + "/json/" + cmdA[1] + "/" + urlHead;
	urlHead = urlHead.substring(0, urlHead.length-1);
	url = urlHead;
	$.post(url, args, function (data) {
	    var text = data.Message;

	    if (data.Status == "ERROR") {
	        text = "Error: " + text;
	        var type = "close";
	    } else if (data.Status == "OK") {
	        var type = "tick";
	    } else if (data.Status == "UNSURE") {
			text = "This action is destructive. Are you sure you want to do this?";
			ans = confirm(text);

			if (ans) {
				_this.input.attr("value", _this.input.attr("value") + " --sure=yes");
				_this.exec();
			}

			return;
	    } else {
			text = "Invalid Command";
			var type = "close";
	    }

	    var lang = "sh";
	    var result = null;

	    _this.input.attr("value", "");
	    _this.status(text, type, cmd, lang, result);
	}, "json");
	break;

    case "csv":
	var lang = "sql";
	// Send to server
	// Get back link
    case "sql": default:
	var lang = "sql";
        var console = this; // Just in case
	$.post("sql/information_schema", {"query": cmd.replace(/^sql/, "")}, function (data) {
            var text = data.Message;

	    if (data.Status == "ERROR") {
	        text = "Error: " + text;
	        var type = "close";
	    } else if (data.Status == "OK") {
	        var type = "tick";
	    } else {
		text = "Invalid Command";
		var type = "close";
	    }

	    var lang = "sql";
	    var table = "<table><thead><tr><th scope='col'>Row</th>";

	    for (var i in data.Columns) {
	        table += "<th scope='col'>" + data.Columns[i] + "</th>";
	    }

	    table += "</tr></thead><tbody>";

	    var row = 0;
	    for (var i in data.Result) {
	        row++;
	        table += "<tr><th scope='row'>" + row + "</th>";
	        for (var j in data.Columns) {
	 	   table += "<td>" + data.Result[i][data.Columns[j]] + "</td>";
	        }
	        table += "</tr>";
	    }

	    table += "</tbody></table>";

	    $("#console input[@type='text']").attr("value", "");
	    console.status(text, type, cmd, lang, table);
	    $("#console .results table").eq(0).tablesorter();
	}, "json");
	break;
    }

    //Draw results

    if (text) {
	this.status(text, type, cmd, lang, result);
	this.input.attr("value", "");
    }

    return false;
};
