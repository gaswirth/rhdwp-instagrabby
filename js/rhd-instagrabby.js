/* ==========================================================================
	Instagrabby JS Helper
	By Roundhouse Designs - https://roundhouse-designs.com
   ========================================================================== */

(function($){
	// Cycle2 Initialization
	$('.rhd-instagrabby').cycle({
		slides: '> li',
		prev: '.cycle-prev',
		next: '.cycle-next',
		fx: 'carousel',
		timeout: 0,
		carouselFluid: true,
		dataAllowWrap: false,
		swipe: true
	});

	// jQuery animation fallback
	if ( !Modernizr.csstransitions ) {
		$(".rhd-instagrabby-container").hover(function(){
			$(this).children('.rhd-instagrabby-pager').fadeToggle();
		});
		alert( 'transitions disabled' );
	}
})(jQuery);