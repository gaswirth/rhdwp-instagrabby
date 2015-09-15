/* ==========================================================================
	Instagrabby JS Helper
	By Roundhouse Designs - https://roundhouse-designs.com
   ========================================================================== */

(function($){
	var newVis;
	var oldVis = parseInt( $('.rhd-instagrabby').attr('data-cycle-carousel-visible'), 10 );

	$(document).ready(function(){
		$('.rhd-instagrabby').cycle({
			log: 'false',
			slides: '> li',
			prev: '.cycle-prev',
			next: '.cycle-next',
			fx: 'carousel',
			timeout: '0',
			carouselFluid: 'true',
			allowWrap: 'false',
			swipe: 'true'
		});
	});

	$('.rhd-instagrabby').on('cycle-bootstrap', function(e, opts, API) {
		checkWindowSize( opts );

		$(window).resize( function(){ checkWindowSize(opts); } );

		$(window).on('orientationchange', function(){
			checkWindowSize( opts );
			$('.rhd-instagrabby').cycle('reinit');
		});
	});

	// jQuery animation fallback
	if ( !Modernizr.csstransitions ) {
		$(".rhd-instagrabby-container").hover(function(){
			$(this).children('.rhd-instagrabby-pager').fadeToggle();
		});
	}

	function checkWindowSize( opts ){
		var w = $(window).width();
		console.log(w);

		if ( w > 720 && w < 800 )
			newVis = 5;
		else if ( w > 640 && w < 720 )
			newVis = 4;
		else if ( w < 640 )
			newVis = 3;
		else
			newVis = false;

		if ( newVis !== false ) {
			opts.carouselVisible = newVis;
		} else {
			newVis = oldVis;
		}
	}

})(jQuery);