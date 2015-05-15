/* ==========================================================================
	Instagrabby JS Helper
   ========================================================================== */

(function($){

	$(document).ready(function(){
		$(".rhd-instagrabby-pager").hover(
			function(){
				$(this).stop().animate({
					opacity: 1
				});
			}, function(){
				$(this).stop().animate({
					opacity: 0
				});
			}
		);
	});

})(jQuery);