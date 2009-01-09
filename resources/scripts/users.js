// Pavel Panchekha

function update() {
    $.getJSON("users/json/all", function (data) {
         for (var i = 0; i < data.length; i++) {
             data[i]["url"] = "users/edit/" + data[i]["name"];
         }
         
         $(".scrolling ul").eq(0).items("replace", data);
    });
}

$(function () {
    $(".selectitem").dblclick(function () {
        $(this).find("a").click();
        return false;
    }).find("a").click(function () {
        alert("Dummy Function: Going to edit " + $(this).parent().find("h2").text());
        // TODO: Replace
        return false;
    });
});