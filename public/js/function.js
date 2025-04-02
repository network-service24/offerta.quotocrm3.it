function scroll_to(id, scarto, tempo) {
    if (scarto == null) {
        scarto = 0
    };
    if (tempo == null) {
        tempo = 300
    };
    $('html,body').animate({
        scrollTop: $('#' + id).offset().top - scarto
    }, {
        queue: false,
        duration: tempo
    });
}

$(document).ready(function() {
    // delegate calls to data-toggle="lightbox"
    $(document).delegate('*[data-gallery="multiimages"]', 'click', function(event) {
        event.preventDefault();
        return $(this).ekkoLightbox({
            always_show_close: true
        });
    });
});

$(document).ready(function() {
    $("#link_msg").click(function() {
        $("#chat").removeAttr("style");
    });
    $("#button_msg").click(function() {
        $("#chat").removeAttr("style");
    });
    $("#button_footer").click(function() {
        $("#chat").removeAttr("style");
    });
    $("#button2_footer").click(function() {
        $("#chat").removeAttr("style");
    });
    $("#button_carta").click(function() {
        $("#carta").removeAttr("style");
    });
    $("#chiudi3").click(function() {
        $("#carta").css("display", "none");
    });
    $("#chiudi2").click(function() {
        $("#chat").css("display", "none");
    });
    $("#chiudi").click(function() {
        $("#msg").css("display", "none");
    });
});

jQuery(document).ready(function() {
    $('.google').addClass('scrolloff');

    $('.GM2').on("mouseup", function() {
        $('.google').addClass('scrolloff');
    });
    $('.GM2').on("mousedown", function() {
        $('.google').removeClass('scrolloff');
    });
    $(".google").mouseleave(function() {
        $('.google').addClass('scrolloff');
    });
});

function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;

}