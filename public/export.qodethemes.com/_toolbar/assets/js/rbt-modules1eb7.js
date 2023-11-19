(function ($) {
	$(window).load(function () {
		rbtAjax();
	});

	// ajax used to request php file
	function rbtAjax() {
		var rbtTheme = $('.rbt-toolbar').data("theme");
		var rbtFeatured = $('.rbt-toolbar').data("featured");
		var rbtButtonPosition = $('.rbt-toolbar').data("button-position");
		var rbtButtonHorizontal = $('.rbt-toolbar').data("button-horizontal");
		var rbtButtonAlt = $('.rbt-toolbar').data("button-alt");
		var rbtAso = getUrlParameter('aso');
		var rbtAca = getUrlParameter('aca');
		var rbtUtmCampaign = getUrlParameter('utm_campaign');
		var rbtReferrer = document.referrer;

		$.ajax({
			url: 'https://toolbar.qodeinteractive.com/templates/profile.php',
			// url: 'http://masterds.localhost/wp-content/plugins/rabbit-toolbar/templates/profile.php', /* LOCAL */
			type: "GET",
			data: {
				theme: rbtTheme,
				featured: rbtFeatured,
				btnpos: rbtButtonPosition,
				btnhorizontal: rbtButtonHorizontal,
				btnalt: rbtButtonAlt,
				aso: rbtAso,
				aca: rbtAca,
				utmCampaign: rbtUtmCampaign,
				referrer: rbtReferrer,
			},
			success: function (data) {
				$('.rbt-toolbar').html(data);
				rbtLazyLoad();
				rbtListToggle();
				rbtSmoothScrollCompatibility();
				showList();
				// rbtLoadScript('https://toolbar.qodeinteractive.com/_toolbar/assets/js/mc-validate.js');
			}
		});
	}

	function rbtLoadScript(url, onSuccess) {
		jQuery.ajax({
			url: url,
			dataType: 'script',
			success: onSuccess,
			async: true
		});
	}

	// lazy-load
	function rbtLazyLoad() {
		
			var imagePlaceholder = new Image();
			$(imagePlaceholder).on('load', function () {
				var load = function() {
					$('.rbt-list .rbt-lazy-load img:not(.rbt-lazy-loading)').each(function (i, object) {
						object = $(object);
						var rect = object[0].getBoundingClientRect(),
							vh = ($(window).height() || document.documentElement.clientHeight),
							vw = ($(window).width() || document.documentElement.clientWidth),
							oh = object.outerHeight(),
							ow = object.outerWidth();
						
						
						if (
							(rect.top != 0 || rect.right != 0 || rect.bottom != 0 || rect.left != 0) &&
							(rect.top >= 0 || rect.top + oh >= 0) &&
							(rect.bottom >= 0 && rect.bottom - oh - vh <= 300) &&
							(rect.left >= 0 || rect.left + ow >= 0) &&
							(rect.right >= 0 && rect.right - ow - vw <= 0)
						) {
							
							object.addClass('rbt-lazy-loading');
							
							var imageObj = new Image();
							
							$(imageObj).on('load', function () {
								var $this = $(this);
								object.attr('src', $this.attr('src'));
								object
									.removeAttr('data-image')
									.removeData('image')
									.removeClass('rbt-lazy-loading');
								object.parent().removeClass('rbt-lazy-load');
							}).attr('src', object.data('image'));
						}
					});
				}
				
				$('.rbt-theme-dropdown .rbt-btn').on('click', function () {
					setTimeout(function(){load();},500); //0.5s is animation time of toolbar showing
				});
				
				$(".rbt-list").scroll(function() {
					load();
				});
				
			}).attr('src', 'https://toolbar.qodeinteractive.com/_toolbar/assets/img/rbt-placeholder.jpg');
	}

	// open/close logic
	function rbtListToggle() {
		var opener = $('.rbt-theme-dropdown .rbt-btn'),
			list = $('.rbt-sidearea'),
			splitScreenPresent = typeof $.fn.multiscroll !== 'undefined' && typeof $.fn.multiscroll.setMouseWheelScrolling !== 'undefined';
			fullPagePresent = typeof $.fn.fullpage !== 'undefined' && typeof $.fn.fullpage.setMouseWheelScrolling !== 'undefined';

		var toggleList = function () {
			opener.on('click', function () {
				if (list.hasClass('rbt-active')) {
					list.removeClass('rbt-active');
					splitScreenPresent && $.fn.multiscroll.setMouseWheelScrolling(true);
					fullPagePresent && $.fn.fullpage.setMouseWheelScrolling(true);
				} else {
					list.addClass('rbt-active');
					splitScreenPresent && $.fn.multiscroll.setMouseWheelScrolling(false);
					fullPagePresent && $.fn.fullpage.setMouseWheelScrolling(false);
				}

				if (list.hasClass('rbt-scrolled')) {
					list.removeClass('rbt-scrolled');
				}
			});
		};

		var currentScroll = $(window).scrollTop();
		$(window).scroll(function () {
			var newScroll = $(window).scrollTop();
			if (Math.abs(newScroll - currentScroll) > 1000) {
				if (list.hasClass('rbt-active')) {
					list.removeClass('rbt-active');
					splitScreenPresent && $.fn.multiscroll.setMouseWheelScrolling(true);
					fullPagePresent && $.fn.fullpage.setMouseWheelScrolling(true);
				}

				if (!list.hasClass('rbt-scrolled')) {
					list.addClass('rbt-scrolled');
				}
			}
		});

		var clickAwayClose = function () {
			$(document).on('click', function (e) {
				if (!list.is(e.target) &&
					list.has(e.target).length === 0 &&
					list.hasClass('rbt-active')) {
					list.removeClass('rbt-active');
					splitScreenPresent && $.fn.multiscroll.setMouseWheelScrolling(true);
					fullPagePresent && $.fn.fullpage.setMouseWheelScrolling(true);
				}
			});
		};

		// init
		if (opener.length) {
			toggleList();
			clickAwayClose();
		}
	}

	// smooth-scroll compatibility
	function rbtSmoothScrollCompatibility() {
		var smoothScrollEnabled = $('body[class*="smooth-scroll"]').length || $('body[class*="smooth_scroll"]').length;

		if (smoothScrollEnabled && !$('html').hasClass('touch')) {
			var opener = $('.rbt-theme-dropdown .rbt-btn'),
				list = $('.rbt-sidearea');

			var disableScroll = function () {
				window.removeEventListener('mousewheel', smoothScrollListener, {passive: false});
				window.removeEventListener('DOMMouseScroll', smoothScrollListener, {passive: false});
			};

			var enableScroll = function () {
				window.addEventListener('mousewheel', smoothScrollListener, {passive: false});
				window.addEventListener('DOMMouseScroll', smoothScrollListener, {passive: false});
			};

			opener
				.on('click', function () {
					setTimeout(function () {
						list.hasClass('rbt-active') ? disableScroll() : enableScroll();
					}, 100);
				});

			list
				.on('mouseenter', function () {
					list.hasClass('rbt-active') && disableScroll();
				})
				.on('mouseleave', function () {
					enableScroll();
				});

		}
	}

	// initial load class
	function showList() {
		var list = $('.rbt-sidearea');

		list.length && list.addClass('rbt-loaded');
	}

	// get url parameter from url
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
})(jQuery);