;(function ($) {
    /*--------------------------------------------------
		SELF HOSTED VIDEO PLAYING ON MODAL OPEN
	--------------------------------------------------*/
	$(".selfPlayVidm").on('shown.bs.modal', function () {
		$(".selfPlayVidm .item.active video").each(function(){
			this.play();
		});
	});
    /*--------------------------------------------------
		SELF HOSTED VIDEO PAUSING ON MODAL CLOSE
    --------------------------------------------------*/
    $(".selfPauseVidm").on('hide.bs.modal', function () {
    	$(".selfPauseVidm .item.active video").each(function(){
			this.pause();
    	});
    });
})(jQuery);