//
// Main.js
//

/* ------------------------------------------------------------------
 * Name         : Wicodus - HTML Template
 * File         : main.js
 * Author       : ScanThemes
 * Author URI   : scanthemes@gmail.com
 *
 * All Rights Reserved.
 * --------------------------------------------------------------------- */
/* ---------------------------------------------------------------------

  [Table of contents]

    - Default options
    - Google Map (Init)
    - Preloader
    - Contact form
    - Scroll To Element
    - Plugins load
    - Google Map
    - Off-Canvas
    - Navbar Affix
    - Modal Window Video (Bootstrap)
    - Carousel (Bootstrap)
    - Progress Bar Animate
    - Animate on scroll
    - Owl-carousel
    - jQuery.NiceScroll
    - Countdown
    - Carousel + animation
    - Count Up
    - Partially collapsing sidebar
    - Collapser

------------------------------------------------------------------------ */
/*---------------------------------------------------------------------- */

"use strict";

//------------------- Default options -------------------
if ( ( options == undefined ) || ( options == false ) ) {
  var options = true,
      preloader = { enabled : false },
      GoogleMapAPI = { 
        key : '',
        language : '',
        region : '',
        disableDefaultUI : false,
        markerIcon: '',
        customMapStyle: ''
      },
      scrollToElement = { scrollSpeed : 700 },
      slider = { interval : 8500 };
}

//----------------------- Init Map ------------------------
function initMap() {
    var map_item = document.querySelectorAll('[data-map]');
    Array.prototype.forEach.call(map_item, function(el) {
        var GM_lat = -34.400,
            GM_lng = 150.9,
            GM_zoom = 8,
            GM_disableDefaultUI = GoogleMapAPI.disableDefaultUI,
            GM_marker_lat = -34.400,
            GM_marker_lng = 150.9,
            GM_marker_title = 'Marker title',
            GM_marker_icon = GoogleMapAPI.markerIcon,
            GM_marker_animation = false,
            GM_marker_label = '',
            GM_marker_content = '',
            GM_style,
            map;

    if ( (el.closest('[data-lat]')) && (el.closest('[data-lng]')) ) {
      GM_lat = Number(el.getAttribute('data-lat'));
      GM_lng = Number(el.getAttribute('data-lng'));
      GM_zoom = Number(el.getAttribute('data-zoom'));
    }
    if ( el.closest('[data-zoom]') ) {
      GM_zoom = Number(el.getAttribute('data-zoom'));
    }
    if ( (el.closest('[data-marker-lat]')) && (el.closest('[data-marker-lng]')) ) {
      GM_marker_lat = Number(el.getAttribute('data-marker-lat'));
      GM_marker_lng = Number(el.getAttribute('data-marker-lng'));
    }
    if ( el.closest('[data-marker-title]') ) {
      GM_marker_title = el.getAttribute('data-marker-title');
    }
    if ( el.closest('[data-marker-animation="drop"]') ) {
      GM_marker_animation = google.maps.Animation.DROP;
    }
    if ( el.closest('[data-marker-animation="bounce"]') ) {
      GM_marker_animation = google.maps.Animation.BOUNCE;
    }
    if ( el.closest('[data-marker-label]') ) {
      GM_marker_label = el.getAttribute('data-marker-label');
    }
    if ( el.closest('[data-marker-icon]') ) {
      GM_marker_icon = el.getAttribute('data-marker-icon');
    }
    if ( el.closest('[data-marker-content]') ) {
      GM_marker_content = el.getAttribute('data-marker-content');
    }
    if ( el.closest('[data-map-style="light"]') ) {
      GM_style = 
        [
          {
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#f5f5f5"
              }
            ]
          },
          {
            "elementType": "labels.icon",
            "stylers": [
              {
                "visibility": "off"
              }
            ]
          },
          {
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#616161"
              }
            ]
          },
          {
            "elementType": "labels.text.stroke",
            "stylers": [
              {
                "color": "#f5f5f5"
              }
            ]
          },
          {
            "featureType": "administrative.land_parcel",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#bdbdbd"
              }
            ]
          },
          {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#eeeeee"
              }
            ]
          },
          {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#e5e5e5"
              }
            ]
          },
          {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#9e9e9e"
              }
            ]
          },
          {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#ffffff"
              }
            ]
          },
          {
            "featureType": "road.arterial",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#dadada"
              }
            ]
          },
          {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#616161"
              }
            ]
          },
          {
            "featureType": "road.local",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#9e9e9e"
              }
            ]
          },
          {
            "featureType": "transit.line",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#e5e5e5"
              }
            ]
          },
          {
            "featureType": "transit.station",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#eeeeee"
              }
            ]
          },
          {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#c9c9c9"
              }
            ]
          },
          {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#9e9e9e"
              }
            ]
          }
        ]
    } else if ( el.closest('[data-map-style="dark"]') ) {
      GM_style = 
        [
          {
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#212121"
              }
            ]
          },
          {
            "elementType": "labels.icon",
            "stylers": [
              {
                "visibility": "off"
              }
            ]
          },
          {
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "elementType": "labels.text.stroke",
            "stylers": [
              {
                "color": "#212121"
              }
            ]
          },
          {
            "featureType": "administrative",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "featureType": "administrative.country",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#9e9e9e"
              }
            ]
          },
          {
            "featureType": "administrative.land_parcel",
            "stylers": [
              {
                "visibility": "off"
              }
            ]
          },
          {
            "featureType": "administrative.locality",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#bdbdbd"
              }
            ]
          },
          {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#181818"
              }
            ]
          },
          {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#616161"
              }
            ]
          },
          {
            "featureType": "poi.park",
            "elementType": "labels.text.stroke",
            "stylers": [
              {
                "color": "#1b1b1b"
              }
            ]
          },
          {
            "featureType": "road",
            "elementType": "geometry.fill",
            "stylers": [
              {
                "color": "#2c2c2c"
              }
            ]
          },
          {
            "featureType": "road",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#8a8a8a"
              }
            ]
          },
          {
            "featureType": "road.arterial",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#373737"
              }
            ]
          },
          {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#3c3c3c"
              }
            ]
          },
          {
            "featureType": "road.highway.controlled_access",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#4e4e4e"
              }
            ]
          },
          {
            "featureType": "road.local",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#616161"
              }
            ]
          },
          {
            "featureType": "transit",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#757575"
              }
            ]
          },
          {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
              {
                "color": "#000000"
              }
            ]
          },
          {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [
              {
                "color": "#3d3d3d"
              }
            ]
          }
        ]
    } else if ( el.closest('[data-map-style="custom"]') ) {
      GM_style = GoogleMapAPI.customMapStyle
    }

    map = new google.maps.Map(el, {
      styles: GM_style,
      center: {lat: GM_lat, lng: GM_lng},
      zoom: GM_zoom,
      disableDefaultUI: GM_disableDefaultUI
    });
    var marker = new google.maps.Marker({
        position: {lat: GM_marker_lat, lng: GM_marker_lng},
        map: map,
        animation: GM_marker_animation,
        title: GM_marker_title,
        label: GM_marker_label,
        icon: GM_marker_icon
    });
    if ( GM_marker_content ) {
      var infowindow = new google.maps.InfoWindow({
          content: GM_marker_content
      });
      marker.addListener('click', function() {
          infowindow.open(map, marker);
      });
    }
  });
}

(function($) {

    //--------------------- Preloader ----------------------
    if ( preloader.enabled == true ) {
        var preloaderBody = document.querySelector('body'),
            preloaderWrapper = document.createElement('div');
        if (preloaderBody.classList) {
            preloaderBody.classList.add("preloader");
        } else {
            preloaderBody.className += ' ' + "preloader";
        }
        if (preloaderWrapper.classList) {
            preloaderWrapper.classList.add("preloaderWrapper");
        } else {
            preloaderWrapper.className += ' ' + "preloaderWrapper";
        }
        document.body.appendChild( preloaderWrapper );
        document.body.onload = function(){
            $('.preloaderWrapper').fadeOut();
            $('body').removeClass('preloader');
        }
    }

    //---------------- Contact form --------------
    if($('[data-form="contact_form"]').length) {
        var form = $('[data-form="contact_form"]'),
            alert_message = '<div class="alert" role="alert" data-alert="result" style="display: none;">',
            form_data,
            SDresult;
        form.prepend(alert_message);
        SDresult = $('[data-alert="result"]');
        // Success function
        function sending_done(response) {
            SDresult.fadeIn().removeClass('alert-danger').addClass('alert-success');
            SDresult.text(response);
            form.find('input:not([type="submit"]), textarea').val('');
        }
        // Fail function
        function sending_fail(data) {
            SDresult.fadeIn().removeClass('alert-success').addClass('alert-danger');
            SDresult.text(data.responseText);
        }
        form.submit(function (e) {
            e.preventDefault();
            form_data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form_data
            })
            .done(sending_done)
            .fail(sending_fail);
        });
    }

    //------------ Scroll To Element ------------
    $('a[data-scroll^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('data-scroll'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top
            },  scrollToElement.scrollSpeed);
        }
    });

    //------------------------ Plugins load ----------------------------- 
    var _mainJS = document.querySelector('#_mainJS[data-plugins="load"]');

    if ( _mainJS ) {
        // Lib
        if ( document.querySelector('[data-animeJS]') ) {
            var anime_js = document.createElement("script");
            anime_js.src = 'assets/plugins/lib/anime/anime.min.js';
            document.body.insertBefore(anime_js, _mainJS);
        }

        // Slick
        if ( document.querySelector('[data-slick]') ) {
            var slick_js = document.createElement("script");
            slick_js.src = 'assets/plugins/slick/slick.min.js';
            document.body.insertBefore(slick_js, _mainJS);
        }

        // Parallax
        if ( document.querySelector('[data-parallax]') ) {
            var parallax_js = document.createElement("script");
            parallax_js.src = 'assets/plugins/parallax/parallax.js';
            document.body.insertBefore(parallax_js, _mainJS);
        }

        // Animate on scroll
        if ( document.querySelector('[data-aos]') ) {
            var aos_js = document.createElement("script");
            aos_js.src = 'assets/plugins/aos/aos.js';
            document.body.insertBefore(aos_js, _mainJS);
        }

        // Countdown
        if ( document.querySelector('[data-countdown]') ) {
            var countdown_js = document.createElement("script");
            countdown_js.src = 'assets/plugins/countdown/jquery.countdown.min.js';
            document.body.insertBefore(countdown_js, _mainJS);
        }

        // Scrollbar
        if ( document.querySelector('[data-scrollbar]') ) {
            var nicescroll_js = document.createElement("script");
            nicescroll_js.src = 'assets/plugins/scrollbar/jquery.nicescroll.min.js';
            document.body.insertBefore(nicescroll_js, _mainJS);
        }

        // Carousel
        if ( (document.querySelector('[data-carousel="owl-carousel"]')) || (document.getElementsByClassName('owl-carousel')) ) {
            var owl_carousel_js = document.createElement("script");
            owl_carousel_js.src = 'assets/plugins/owl.carousel/owl.carousel.min.js';
            document.body.insertBefore( owl_carousel_js, _mainJS);
        }

    }

  //----------- Google Map ----------
  if ( document.querySelector('[data-map]') ) {
    var GM_item = document.querySelector('[data-map]'),
        GM_map  = document.createElement("script"),
        GM_api  = 'https://maps.googleapis.com/maps/api/js?key=',
        GM_key  =  GoogleMapAPI.key,
        GM_init = '&callback=initMap',
        GM_lang = GoogleMapAPI.language,
        GM_reg  = GoogleMapAPI.region,
        GM_src;
    if (GM_lang) {
        GM_lang = '&language=' + GM_lang;
    }
    if (GM_reg) {
        GM_reg = '&region=' + GM_reg;
    }
    GM_src = GM_api + GM_key + GM_init + GM_lang + GM_reg;
    GM_map.src = GM_src;
    document.body.insertBefore( GM_map, _mainJS);
  }

  //------------- Off-Canvas ---------------
  $('[data-toggle="offcanvas"]').on('click', function(e) {
      e.preventDefault();
      var offcanvas_target = $(this).data("target");
      if ($(offcanvas_target).is('.show')) {
          $(offcanvas_target).addClass("showing").delay(300).queue(function(next){
              $(this).removeClass("showing");
              next();
          });
          $(offcanvas_target).removeClass('show');
          $('.overlay-offcanvas').remove();
      } else {
          $(offcanvas_target).addClass("showing").delay(300).queue(function(next){
              $(this).removeClass("showing");
              next();
          });
          $(offcanvas_target).addClass('show');
          $('<div data-offcanvas-overlay="' + offcanvas_target + '-overlay" class="overlay-offcanvas"></div>').insertBefore(offcanvas_target);
      }
  });

  /* offcanvas-close */
  $('[data-toggle="offcanvas-close"]').on('click', function() {
      var offcanvas_close_item = $(this).closest('[class*="offcanvas"]').attr('id'),
          offcanvas_close_overlay = ( '#' + offcanvas_close_item + '-overlay' );
      $( '#' + offcanvas_close_item + '.show' ).addClass("showing").delay(300).queue(function(next){
          $(this).removeClass("showing");
          next();
      });
      $( '#' + offcanvas_close_item + '.show' ).removeClass('show');
      $( '[data-offcanvas-overlay="' + offcanvas_close_overlay + '"]').remove();
  });

  /* offcanvas-overlay */
  $(document).on('click', '[data-offcanvas-overlay]', function() {
      var offcanvas_overlay_item = $(this).next('.offcanvas');
      $( offcanvas_overlay_item ).addClass("showing").delay(300).queue(function(next){
          $(this).removeClass("showing");
          next();
      });
      $(offcanvas_overlay_item).removeClass('show');
      $(this).remove();
  });

  //----------- Navbar Affix -----------------
  $(window).on('scroll', function (event) {
      var scrollValue = $(window).scrollTop();
      if (scrollValue > 60) {
          $('.navbar-affix').addClass('affix');
      } else{
          $('.navbar-affix').removeClass('affix');
      }
  });

  //----------- Modal Window Video (Bootstrap) ----------- 
  $('[data-modal-video="play"]').on('click', function () {
      var theModal = $(this).data("target"),
      videoSRC = $(this).attr("data-video"),
      videoSRCauto = videoSRC + "?modestbranding=1&rel=0&controls=0&showinfo=0&html5=1&autoplay=1";
      $(theModal + ' iframe').attr('src', videoSRCauto);
      $(theModal + ' button.close').on('click', function () {
          $(theModal + ' iframe').attr('src', videoSRC);
      });
  });

  //----------- Carousel (Bootstrap) -----------
  $('#slider_fade').carousel({
      interval: slider.interval // set interval
  });

  //----------- Progress Bar Animate ---------------
  function progressbar_animate() {
      $('.progress .progress-bar').css("width",
          function() {
              return $(this).attr("aria-valuenow") + "%";
          }
      );
  }

  $(window).on('load', function () {
    //------------- Animate on scroll --------------
    if ( $('[data-aos]').length ) {
        AOS.init({
            // Global settings
            duration: 1500,
            easing: 'ease',
        });
        $(window).on('scroll', function (event) {
            if ($('[data-aos]').is('.aos-animate')) {
              setTimeout(progressbar_animate, 800);
            }
        });
    }

    //------------- Owl-carousel --------------
    if ( document.querySelector('.owl-carousel') ) {
        $( '.owl-carousel' ).each(function() {
            var owl_col = [1, 2, 3, 4],
                owl_margin = 0,
                owl_loop = true,
                owl_center = false,
                owl_mouseDrag = true,
                owl_touchDrag = true,
                owl_pullDrag = true,
                owl_freeDrag = false,
                owl_autoWidth = false,
                owl_startPosition = 0,
                owl_nav = false,
                owl_rewind = true,
                owl_navText = ['<span class="ti-angle-left"></span>','<span class="ti-angle-right"></span>'],
                owl_navElement = 'button',
                owl_slideBy = 1,
                owl_slideTransition = '',
                owl_dots = false,
                owl_autoplay = false,
                owl_autoplayTimeout = 5000,
                owl_autoplayHoverPause = false,
                owl_video = false,
                owl_smartSpeed = 250,
                owl_lazyLoad = false,
                owl_animateOut = false,
                owl_animateIn = false,
                owl_stagePadding = 0;
                
            if( $(this).attr('data-carousel-items') ) {
              owl_col = $(this).attr('data-carousel-items').split(",").map(Number);
            }
            if( $(this).attr('data-carousel-margin') ) {
              owl_margin = parseInt($(this).attr('data-carousel-margin'), 10);
            }
            if( $(this).attr('data-carousel-loop') ) {
              owl_loop = JSON.parse($(this).attr('data-carousel-loop'));
            }
            if( $(this).attr('data-carousel-center') ) {
              owl_center = JSON.parse($(this).attr('data-carousel-center'));
            }
            if( $(this).attr('data-carousel-mouseDrag') ) {
              owl_mouseDrag = JSON.parse($(this).attr('data-carousel-mouseDrag'));
            }
            if( $(this).attr('data-carousel-touchDrag') ) {
              owl_touchDrag = JSON.parse($(this).attr('data-carousel-touchDrag'));
            }
            if( $(this).attr('data-carousel-pullDrag') ) {
              owl_pullDrag = JSON.parse($(this).attr('data-carousel-pullDrag'));
            }
            if( $(this).attr('data-carousel-freeDrag') ) {
              owl_freeDrag = JSON.parse($(this).attr('data-carousel-freeDrag'));
            }
            if( $(this).attr('data-carousel-stagePadding') ) {
              owl_stagePadding = parseInt($(this).attr('data-carousel-stagePadding'), 10);
            }
            if( $(this).attr('data-carousel-autoWidth') ) {
              owl_autoWidth = JSON.parse($(this).attr('data-carousel-autoWidth'));
            }
            if( $(this).attr('data-carousel-startPosition') ) {
              owl_startPosition = JSON.parse($(this).attr('data-carousel-startPosition'));
            }
            if( $(this).attr('data-carousel-nav') ) {
              owl_nav = JSON.parse($(this).attr('data-carousel-nav'));
            }
            if( $(this).attr('data-carousel-rewind') ) {
              owl_rewind = JSON.parse($(this).attr('data-carousel-rewind'));
            }
            if( $(this).attr('data-carousel-navText') ) {
              owl_navText = $(this).attr('data-carousel-navText').split(",");
            }
            if( $(this).attr('data-carousel-navElement') ) {
              owl_navElement = $(this).attr('data-carousel-navElement');
            }
            if( $(this).attr('data-carousel-slideBy') ) {
              owl_slideBy = parseInt($(this).attr('data-carousel-slideBy'), 10);
            }
            if( $(this).attr('data-carousel-slideTransition') ) {
              owl_slideTransition = $(this).attr('data-carousel-slideTransition');
            }
            if( $(this).attr('data-carousel-dots') ) {
              owl_dots = JSON.parse($(this).attr('data-carousel-dots'));
            }
            if( $(this).attr('data-carousel-autoplay') ) {
              owl_autoplay = JSON.parse($(this).attr('data-carousel-autoplay'));
            }
            if( $(this).attr('data-carousel-autoplayTimeout') ) {
              owl_autoplayTimeout = parseInt($(this).attr('data-carousel-autoplayTimeout'), 10);
            }
            if( $(this).attr('data-carousel-autoplayHoverPause') ) {
              owl_autoplayHoverPause = JSON.parse($(this).attr('data-carousel-autoplayHoverPause'));
            }
            if( $(this).attr('data-carousel-smartSpeed') ) {
              owl_smartSpeed = parseInt($(this).attr('data-carousel-smartSpeed'), 10);
            }
            if( $(this).attr('data-carousel-video') ) {
              owl_video = JSON.parse($(this).attr('data-carousel-video'));
            }
            if( $(this).attr('data-carousel-lazyLoad') ) {
              owl_lazyLoad = JSON.parse($(this).attr('data-carousel-lazyLoad'));
            }
            if( $(this).attr('data-carousel-animateOut') ) {
              owl_animateOut = $(this).attr('data-carousel-animateOut');
            }
            if( $(this).attr('data-carousel-animateIn') ) {
              owl_animateIn = $(this).attr('data-carousel-animateIn');
            }
            $(this).owlCarousel({
              margin: owl_margin,
              dots: owl_dots,
              loop: owl_loop,
              center: owl_center,
              mouseDrag: owl_mouseDrag,
              touchDrag: owl_touchDrag,
              pullDrag: owl_pullDrag,
              stagePadding: owl_stagePadding,
              autoWidth: owl_autoWidth,
              startPosition: owl_startPosition,
              nav: owl_nav,
              navText: owl_navText,
              navElement: owl_navElement,
              rewind: owl_rewind,
              slideBy: owl_slideBy,
              slideTransition: owl_slideTransition,
              autoplay: owl_autoplay,
              autoplayTimeout: owl_autoplayTimeout,
              autoplayHoverPause: owl_autoplayHoverPause,
              smartSpeed: owl_smartSpeed,
              video: owl_video,
              lazyLoad: owl_lazyLoad,
              animateOut: owl_animateOut,
              animateIn: owl_animateIn,
              responsive: {
                0: {
                  items: owl_col[0]
                },
                576: {
                  items: owl_col[1]
                },
                992: {
                  items: owl_col[2]
                },
                1200: {
                  items: owl_col[3]
                }
              }
            });
        });
    }

    //------------- jQuery.NiceScroll --------------
    if ( document.querySelector('[data-scrollbar="scroll"]') ) {
        $( '[data-scrollbar="scroll"]' ).each(function() {
          var nicescroll = {
              cursorcolor : '#bbbbbb',
              cursorborder : 0,
              cursorwidth : '8px',
              hwacceleration: true,
              iframeautoresize: true,
              disableoutline: true,
              smoothscroll: true,
              enablescrollonselection: true,
              enableobserver: true,
              zindex : 10
          };
          if( $(this).attr('data-scrollbar-color') ) {
            nicescroll.cursorcolor = $(this).attr('data-scrollbar-color');
          }
          if( $(this).attr('data-scrollbar-border') ) {
            nicescroll.cursorborder = $(this).attr('data-scrollbar-border');
          }
          if( $(this).attr('data-scrollbar-width') ) {
            nicescroll.cursorwidth = $(this).attr('data-scrollbar-width');
          }
          if( $(this).attr('data-scrollbar-zindex') ) {
            nicescroll.zindex = parseInt($(this).attr('data-scrollbar-zindex'), 10);
          }
          $(this).niceScroll({
            cursorcolor: nicescroll.cursorcolor,
            cursorborder: nicescroll.cursorborder,
            cursorwidth: nicescroll.cursorwidth,
            hwacceleration: nicescroll.hwacceleration,
            iframeautoresize: nicescroll.iframeautoresize,
            disableoutline: nicescroll.disableoutline,
            smoothscroll: nicescroll.smoothscroll,
            enablescrollonselection: nicescroll.enablescrollonselection,
            enableobserver: nicescroll.enableobserver,
            zindex: nicescroll.zindex
          });
        });
    }

    //------------ Countdown -------------
    // countdown default
    $('.countdown-default').each(function() {
        var $countdown_default = $(this),
            finalDate;
        if( $countdown_default.attr('data-countdown') ) {
            finalDate = $countdown_default.data('countdown');
        } else {
            finalDate = 0;
        }
        $countdown_default.countdown(finalDate, function(event) {
          $countdown_default.html(event.strftime('<div class="row gutters-y">'
            + '<div class="col"><h1>%w</h1> Weeks </div>'
            + '<div class="col"><h1>%d</h1> Days </div>'
            + '<div class="col"><h1>%H</h1> Hours </div>'
            + '<div class="col"><h1>%M</h1> Minutes </div>'
            + '<div class="col"><h1>%S</h1> Seconds </div>'));
        });
    });

    // countdown outline
    $('.countdown-outline').each(function() {
        var $countdown_outline = $(this),
            finalDate;
        if( $countdown_outline.attr('data-countdown') ) {
            finalDate = $countdown_outline.data('countdown');
        } else {
            finalDate = 0;
        }
        $countdown_outline.countdown(finalDate, function(event) {
          $countdown_outline.html(event.strftime('<div class="row gutters-y">'
            + '<div class="col countdown-outline-box"><h2>%d</h2> Days </div>'
            + '<div class="col countdown-outline-box"><h2>%H</h2> Hours </div>'
            + '<div class="col countdown-outline-box"><h2>%M</h2> Minutes </div>'
            + '<div class="col countdown-outline-box"><h2>%S</h2> Seconds </div>'));
        });
    });

    // countdown coupon
    $('.countdown-coupon').each(function() {
        var $countdown_coupon = $(this),
            finalDate;
        if( $countdown_coupon.attr('data-countdown') ) {
            finalDate = $countdown_coupon.data('countdown');
        } else {
            finalDate = 0;
        }
        $countdown_coupon.countdown(finalDate)
        .on('update.countdown', function(event) {
            var format = '%H:%M:%S';
            if(event.offset.totalDays > 0) {
              format = '%-d day%!d ' + format;
            }
            if(event.offset.weeks > 0) {
              format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
        })
        .on('finish.countdown', function(event) {
            $(this).html('This offer has expired!')
            .parent().addClass('disabled');
        });
      });

    //------------------------- Slick -------------------------
    if ( document.querySelector('[data-slick]') ) {
      $('[data-slick="product-body"]').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.product-slider-nav'
      });
      $('.product-slider-nav').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '[data-slick="product-body"]',
        dots: false,
        centerMode: true,
        focusOnSelect: true
      });
    }
  });

  //------------ Carousel + animation -------------
  $('#carousel_main').on('slide.bs.carousel', function () {
    $('.carousel-inner .carousel-item').find('[data-carousel-animation]').removeClass('active');
    $('.carousel-inner .carousel-item.active').find('[data-carousel-animation]').addClass('active');
  })

  //------------ Count Up -------------
  if ( document.querySelector('.counter') ) {
    var counterActive = 0;
    $.fn.isInViewport = function() {
      var elementTop = $(this).offset().top,
          elementBottom = elementTop + $(this).outerHeight(),
          viewportTop = $(window).scrollTop(),
          viewportBottom = viewportTop + $(window).height();
      return elementBottom > viewportTop && elementTop < viewportBottom;
    };
    $(window).on('load resize scroll', function() {
        if ($('.counter').isInViewport() && counterActive == 0 ) {
          $('.counter').each(function() {
                var $counter_item = $(this),
                    countTo = $counter_item.attr('data-count'),
                    animation = { countNum: countTo },
                    sec = 1000,
                    duration = 4 * sec,
                    EPV =  0.9989,
                    start = 0,
                    prefix = '',
                    suffix = '',
                    decimal;

                // Start
                if( $counter_item.attr('data-start') ) {
                    $counter_item.text($counter_item.attr('data-start'));
                } else {
                    $counter_item.text('0');
                }

                // Duration
                if( $counter_item.attr('data-duration') ) {
                    duration = Math.floor($counter_item.attr('data-duration') * sec);
                } else {
                    duration = Math.floor(4 * sec); // default
                }

                // Prefix
                if( $counter_item.attr('data-prefix') ) {
                    prefix = $counter_item.attr('data-prefix');
                } else {
                    prefix = '';
                }

                // Suffix
                if( $counter_item.attr('data-suffix') && prefix == '' ) {
                    suffix = $counter_item.attr('data-suffix');
                } else {
                    suffix = '';
                }

                // Decimal
                if( $counter_item.attr('data-decimal') ) {
                    decimal = $counter_item.attr('data-decimal');
                } else {
                    decimal = 0;
                }

                // Easing
                if( $counter_item.attr('data-easing') == 'false' ) {
                    $({ countNum: $counter_item.text()}).animate(animation, {
                        duration: 4000,
                        easing:'linear',
                        step: function() {
                            if(decimal > 0 && decimal <= 3) {
                                $counter_item.text(prefix + parseFloat(this.countNum).toFixed(decimal) + suffix);
                            } else {
                                $counter_item.text(prefix + parseInt(this.countNum, 10) + suffix);
                            }
                        },
                        complete: function() {
                            $counter_item.text(prefix + parseInt(this.countNum, 10) + suffix);
                        }
                    });
                } else {
                    $({ countNum: $counter_item.text() }).animate(animation, {
                        duration: duration,
                        step: function (SD, EP) {
                            if(decimal > 0 && decimal <= 3) {
                                $counter_item.text(prefix + parseFloat(this.countNum).toFixed(decimal) + suffix);
                            } else {
                                $counter_item.text(prefix + parseInt(this.countNum, 10) + suffix);
                            }
                            if (EP.pos > EPV) {
                                $(this).stop();
                                $(this).animate(animation, {
                                    duration: 300,
                                    step: function () {
                                        if(decimal > 0 && decimal <= 3) {
                                            $counter_item.text(prefix + parseFloat(this.countNum).toFixed(decimal) + suffix);
                                        } else {
                                            $counter_item.text(prefix + parseInt(this.countNum, 10) + suffix);
                                        }
                                    },
                                    complete: function () {
                                        if(decimal > 0 && decimal <= 3) {
                                            $counter_item.text(prefix + parseFloat(this.countNum).toFixed(decimal) + suffix);
                                        } else {
                                            $counter_item.text(prefix + parseInt(this.countNum, 10) + suffix);
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
          });
          counterActive = 1;
        }
    });
  }

  //-------------------- Partially collapsing sidebar -------------------
  $('[data-collapsing="partially"]').on('click', function () {
      var p_collapsing_item = $(this).attr('data-collapsing-target');
      if($(p_collapsing_item).is('.p-collapse')) {
        $(p_collapsing_item).removeClass('p-collapse');
      } else {
        $(p_collapsing_item).addClass('p-collapse');
      }
  });

  //------------------------- Collapser -------------------------
  $('.collapser:not(.readmore-btn)').on('click', function() {
      if($(this).is('.collapser-active')) {
        $(this).removeClass('collapser-active');
      } else {
        $(this).addClass('collapser-active');
      }
      $(this).next().collapse('toggle');
  });

  //------------------------- Collapser (Read More) -------------------------
  $('.readmore-btn.collapser').on('click', function() {
      if($(this).is('.collapser-active')) {
        $(this).removeClass('collapser-active');
      } else {
        $(this).addClass('collapser-active');
      }
      $(this).prev().collapse('toggle');
  });

  //----------------------- Button Number Incrementers ---------------------
  if ( document.querySelector('.quantity-group') ) {
    $(".price-wrapper .quantity-group").append('<div class="quantity_wrapper"><div class="inc btn-quantity">+</div><div class="dec btn-quantity">-</div></div>');
      $(".btn-quantity").on("click", function() {
      var $button = $(this),
          oldValue = $button.closest(".quantity-group").find("input").val();
      if ($button.text() == "+") {
        var newVal = parseFloat(oldValue) + 1;
      } else {
        if (oldValue > 0) {
          var newVal = parseFloat(oldValue) - 1;
        } else {
          newVal = 0;
        }
      }
      $button.closest(".quantity-group").find("input").val(newVal);

    });
  }

})( jQuery );




