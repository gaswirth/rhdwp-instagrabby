/* ==========================================================================
	Instagrabby JS Helper
   ========================================================================== */

(function($){
	$(document).ready(function(){
		// jQuery animation fallback
		if ( !Modernizr.csstransitions ) {
			$(".rhd-instagrabby-container").hover(function(){
				$(this).children('.rhd-instagrabby-pager').fadeToggle();
			});
			alert( 'transitions disabled' );
		}
	});
})(jQuery);