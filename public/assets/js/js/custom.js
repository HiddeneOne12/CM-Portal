/* Custom js */

$(document).ready(function() {
    
    $(window).scroll(function() {

        if ($(window).scrollTop() > 100) {
            $('.wprmenu_bar').addClass('sticky');
        } else {
            $('.wprmenu_bar').removeClass('sticky');
        }
    });
    
    $(window).scroll(function() {

        if ($(window).scrollTop() > 100) {
            $('.header-area').addClass('sticky-main-menu');
        } else {
            $('.header-area').removeClass('sticky-main-menu');
        }
    });
    
    $(".banner-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".banner").offset().top
        }, 1000);
    });
    $(".happiness-needs-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".happiness-needs").offset().top
        }, 1000);
    });
    $(".our-goals-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".our-goals").offset().top
        }, 1000);
    });
    $(".our-members-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".our-members").offset().top
        }, 1000);
    });
    $(".our-teams-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".our-teams").offset().top
        }, 1000);
    });
    $(".counter-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".counter").offset().top
        }, 1000);
    });
    $(".ahmed-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".ahmed").offset().top
        }, 1000);
    });
    $(".partners-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".partners").offset().top
        }, 1000);
    });
     $(".app-click").click(function() {
        $('html, body').animate({
            scrollTop: $(".app").offset().top
        }, 1000);
    });
    
});

document.addEventListener('DOMContentLoaded', function() {
    var options = {};
    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems, options);
  });

  // Or with jQuery

  $(document).ready(function(){
    $('select').formSelect();
  });

  /*==============================
=            Slider            =
==============================*/

$(document).ready(function(){
  $('.owl-carousel').owlCarousel({
	  autoplay: true,
	  autoPlay: 4000,
      pagination: true,
      loop:true,
      margin:10,
      nav:true,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:3
          },
          1000:{
              items:5
          }
      }
  });
});

/*=====  End of Slider  ======*/

/*==============================
=            Slider-MSG           =
==============================*/

$(document).ready(function(){
  $('.owl-carousel-msg').owlCarousel({
	  items:1,
	  autoplay: true,
	  autoPlay: 4000,
      pagination: true,
      loop:true,
      margin:10,
      nav:true,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:1
          },
          1000:{
              items:1
          }
      }
  });
});

/*=====  End of Slider  ======*/

/*==============================
=           App Slider            =
==============================*/

$(document).ready(function(){
  $('.owl-app-carousel').owlCarousel({
	  autoplay: true,
      loop:true,
      margin:10,
      nav:true,
           items : 1,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:3
          },
          1000:{
              items:5
          }
      }
  });
   function top_align() {
    $(window).scrollTop(0);
    console.log('move');
  }
});

/*=====  End of App Slider  ======*/

/*===============================
=            Counter            =
===============================*/

$(document).ready(function(){
  return 0;
  
  /*$('.count').each(function () {
      $(this).prop('Counter',0).animate({
          Counter: $(this).text()
      }, {
          duration: 10000,
          easing: 'swing',
          step: function (now) {
              $(this).text(Math.ceil(now));
          }
      });
  });*/
});

/*=====  End of Counter  ======*/


/*$(window).scroll(function(){
    $('.testing').each(function(){
      if(isScrolledIntoView($(this))){

        $('.count').each(function () {
            $(this).prop('Counter',0).animate({
                Counter: $(this).text()
            }, {
                duration: 1000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
        
      }
    });
});

function isScrolledIntoView(elem){
  var $elem = $(elem);
  var $window = $(window);

  var docViewTop = $window.scrollTop();
  var docViewBottom = docViewTop + $window.height();

  var elemTop = $elem.offset().top;
  var elemBottom = elemTop + $elem.height();

  return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));

}*/