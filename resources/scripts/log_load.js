// Pavel Panchekha

tullok.load_time = new Date();
$(function () {
    $.post("log/load", {"time": new Date() - tullok.load_time});
});