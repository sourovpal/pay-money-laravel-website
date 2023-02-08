"use strict";

$("#switch").on('click', function () {
    if ($("body").hasClass("dark")) {
        $("body").removeClass("dark");
        $(this).find('img').toggle();
        $("#switch").removeClass("switched");
        localStorage.setItem('theme', 'light');

    }
    else {
        $("body").addClass("dark");
        $(this).find('img').toggle();
        $("#switch").addClass("switched");
        localStorage.setItem('theme', 'dark');
    }
});

var theme = localStorage.getItem('theme');

if(theme == 'dark') {
    $("body").addClass("dark");
    $("img.sun").removeClass("img-none")
    $("img.moon").addClass("img-none")
    $("#switch").addClass("switched");
} else {
    $("body").removeClass("dark");
    $("img.moon").removeClass("img-none")
    $("img.sun").addClass("img-none")
    $("#switch").removeClass("switched");
}

(function () {
    var carousels = function () {
      $(".owl-carousel1").owlCarousel({
        loop: true,
        center: true,
        margin: 0,
        responsiveClass: true,
        nav: false,
        responsive: {
          0: {
            items: 1,
            nav: false
          },
          680: {
            items: 2,
            nav: true,
            loop: true
          },
          991: {
            items: 3,
            nav: true
          }
        }
      });
    };

    (function ($) {
      carousels();
    })(jQuery);
  })();

$(document).ready(function(){
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();
        if (scroll > 95) {
          $(".bg-white").addClass("shadow-sm");
        }
        else{
            $(".start-header").removeClass("shadow-sm");
        }
    })
  })
