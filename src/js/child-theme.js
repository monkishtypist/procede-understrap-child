/**
 * Nav Fix for Dropdown Gaps (Bootstrap)
 *
 * This jQuery fixes hover state issues when the menu dropdown
 * has a gap from its parent nav item. This adds a slight delay
 * to allow the cursor to cross the gap before the dropdown is
 * closed.
 *
 * Nav fix to hide menu on #anchor click
 *
 * Removes `.show` class from Nav elements on subnav click.
 */
(function( $ ) {
	'use strict';

	var timer;
	var ref;

	$("#main-menu > .nav-item")
		.on("mouseover", showDropdown)
		.on("mouseleave", function(){
			timer = setTimeout( hideDropdown, 250);
		});

	$("#main-menu > .nav-item .dropdown-menu .dropdown-item")
		.on("click", function(){
			$("#main-menu .show").removeClass("show");
		});

	function showDropdown() {
		clearTimeout(timer);
		hideDropdown();
		ref = $(this);
		ref.addClass("show").find(".dropdown-menu").addClass("show");
	}
	function hideDropdown() {
		if (ref)
			ref.removeClass("show").find(".dropdown-menu").removeClass("show");
	}

	var navTopH = $('#top-nav').height();
	var navMainH = $('#main-nav').height();
	var navHeight = navTopH + navMainH;

})(jQuery);

/**
 * Set Carousel Item heights
 *
 * The following jQuery sets the height of each `.carousel-item` 
 * to the height of the largest carousel item in the set. This 
 * prevents unusual resizing issues for dynamic text lengths.
 */
(function($) {
	function setCarouselHeight($carousel) {
		var id = $carousel.attr("id");
		var maxCarouselItemHeight = Math.max.apply(null, $carousel.find(".carousel-item").map(function (){
			return $(this).height();
		}).get());
		if(maxCarouselItemHeight > 0){
			$carousel.find(".carousel-item").each(function(){
				$(this).css("height", maxCarouselItemHeight);
			});
		}
	}
	var $carousels = $(".carousel");
	$carousels.each(function(){
		setCarouselHeight($(this));
	});
	$(window).resize(function(){
		$carousels.each(function(){
			$(this).find(".carousel-item").each(function(){
				$(this).css("height", "");
			});
			setCarouselHeight($(this));
		});
	});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		var t = $(e.target).attr("aria-controls");
		setCarouselHeight($('#'+t).find(".carousel"));
	});
})(jQuery);

/**
 * Modernizr Fix for `object-fit`
 */
(function($){
	$.fn.addImageToWrapperBg = function(){
		$(this).each(function () {
			var $container = $(this),
				imgUrl     = $container.find('img').prop('src');
			if (imgUrl) {
				$container
					.css('backgroundImage', 'url(' + imgUrl + ')')
					.addClass('compat-object-fit');
			}  
		});
	}
	if ( ! Modernizr.objectfit ) {
		$('.card-header__img-wrapper').addImageToWrapperBg();
		$('.object-fit__img-wrapper').addImageToWrapperBg();
	}
})(jQuery);


//
// https://github.com/sukhoi1, 25 Jan 2016
// bicubic-img-interpolation v0.1.0 jQuery plugin

// jQuery.fn.bicubicImgInterpolation = function(settings) {
// 	var self = this;
// 	jQuery(self).each(function() {
// 		if(this.tagName === "IMG") {
// 			var src = jQuery(this).attr('src');
// 			var imgW = this.width;
// 			var imgH = this.height;
// 			jQuery(this).after("<canvas style='display: none' width='" + imgW +  "' height='" + imgH + "'></canvas>");
// 			var can = jQuery(this).next()[0];
// 			var callback = drawHighResolutionImgThumbnail;
// 			drawCanvas(can, imgW*6, imgH*6, src, callback, this, settings.crossOrigin);
// 		}
// 	});

// 	function drawCanvas(can, imgW, imgH, src, callback, imgEl, crossOrigin) {

// 		var ctx = can.getContext('2d');
// 		var img = new Image();
// 		if(crossOrigin) {
// 			img.setAttribute('crossOrigin', 'anonymous'); //tainted canvases may not be exported chrome, ie will also throw security error
// 		}

// 		var w = imgW;
// 		var h = imgH;

// 		img.onload = function() {

// 			// Step it down several times
// 			var can2 = document.createElement('canvas');
// 			can2.width = w;
// 			can2.height = h;
// 			var ctx2 = can2.getContext('2d');

// 			// Draw it at 1/2 size 3 times (step down three times), making sure we don't overlap it
// 			ctx2.drawImage(img, 0, 0, w/2, h/2);
// 			ctx2.drawImage(can2, 0, 0, w/2, h/2, w/2, h/2, w/4, h/4);
// 			ctx.drawImage(can2, w/2, h/2, w/4, h/4, 0, 0, w/6, h/6);
// 			if(callback) {
// 				callback(can, this.src, imgEl);
// 			}
// 		};

// 		img.src = src;
// 	};

// 	function drawHighResolutionImgThumbnail(can, attrSrc, imgEl) {
// 		jQuery(imgEl).attr('src', can.toDataURL("image/png"));
// 		jQuery(imgEl).attr('data-src', attrSrc);
// 	};
// };

// jQuery( document ).ready(function() {
// 	if (navigator.appName == 'Microsoft Internet Explorer' || navigator.appName == 'Netscape' || !!(navigator.userAgent.match(/Trident/) || navigator.userAgent.match(/rv:11/)) || (typeof $.browser !== "undefined" && $.browser.msie == 1)){
// 		jQuery('img').bicubicImgInterpolation({});
// 	}
// });

