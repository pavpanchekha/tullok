// Pavel Panchekha and Kamran Khan

$(function () {
    $("#submit").click(function () {
        db = $(".confirm .line").text(); // Database we're deleting
        $.post("databases/json/delete/" + db, // Send data: database name, that we're sure, that we want json
            {"dbName": db, "sure": "yes"}, function (data) {
                if (data.Status == "OK") {
                    self.parent.tullok.console.status(data.Message, "tick", data.SQL, "sql");
                    self.parent.update();
                } else {
                    self.parent.tullok.console.status(data.Message, "close", data.SQL, "sql");
                }

                self.parent.tb_remove(); // Close thickbox.
            }, "json");

        $("#menu a[@rel=delForm-show]", self.parent.document).click();
        return false;
    });

    $("#cancel").click(function () {
	self.parent.tb_remove(); // Close thickbox
        return false;
    });
});
