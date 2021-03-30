$(document).ready(function () {
    $(".conform .block_content p:not(:first)").each(function () {
        $(this).hide(); // cache les champs
    });
    $(".conform .block_content p:first").addClass("current");
    setTimeout("slide()", 3000);
});

function slide() {
    var prev = $(".conform .block_content p.current");
    if ($(".conform .block_content p.current").next().size() > 0) {
        var next = $(".conform .block_content p.current").next();
    } else {
        var next = $(".conform .block_content p:first");
    }
    prev.removeClass("current");
    next.addClass("current");
    prev.fadeOut();
    next.fadeIn();
    setTimeout("slide()", 3000);


}

div.conform .block_content{ position:relative; padding:10px;}