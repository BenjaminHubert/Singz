;(function ($) {
    /*--------------------------------------------------
       SELF HOSTED VIDEO PAUSING ON MODAL CLOSE
    --------------------------------------------------*/
    //It will work on .pauseVideoM class only
    $(".selfPauseVidm").on('hide.bs.modal', function () {
      $("video").each(function(){
        this.pause();
      });
    });
})(jQuery);