/*
    JavaScript For Responsive Bootstrap Portfolio And Lightbox

    Author: ThemeCuriosity
    Item Name: Responsive Bootstrap Portfolio And Lightbox
    Author URI: https://codecanyon.net/user/themecuriosity
    Description: Different Types of Bootstrap Portfolios And Lightboxes

*/

;(function ($) {
    /*--------------------------------------------------
       CAROUSEL SLIDING DURATION
    --------------------------------------------------*/
    var slideDurationValue = $(".carousel").attr("data-duration");

    if (isNaN(slideDurationValue) || slideDurationValue <= 0){
        $.fn.carousel.Constructor.TRANSITION_DURATION = 1000;
        $(".carousel-inner > .item").css({
        '-webkit-transition-duration': slideDurationValue + '1000ms',
        '-moz-transition-duration': slideDurationValue + '1000ms',
        'transition-duration': slideDurationValue + '1000ms'
        });
    }else{
        $.fn.carousel.Constructor.TRANSITION_DURATION = slideDurationValue;
        $(".carousel-inner > .item").css({
        '-webkit-transition-duration': slideDurationValue + 'ms',
        '-moz-transition-duration': slideDurationValue + 'ms',
        'transition-duration': slideDurationValue + 'ms'
        });
    }
    /*--------------------------------------------------
       CAROUSEL MOUSE WHEEL
    --------------------------------------------------*/
    var mouseWheelY = $(".carousel").find('[class=mouse_wheel_y]');
    var mouseWheelXY = $(".carousel").find('[class=mouse_wheel_xy]');

    if(mouseWheelXY){
        $('.mouse_wheel_xy').bind('mousewheel', function(e){
            if(e.originalEvent.wheelDelta /120 > 0) {
                $(this).carousel('prev');
            }else {
                $(this).carousel('next');
            }
        });
    }if(mouseWheelY){
        $('.mouse_wheel_y').bind('mousewheel', function(e){
            if(e.originalEvent.wheelDelta /120 > 0) {
                $(this).carousel('next');
            }
        });
    }
    /*--------------------------------------------------
        THUMBNAILS INDICATORS SCROLL
    --------------------------------------------------*/
    "use strict";
    var indicatorPositionY = 0;
    var indicatorPositionX = 0;
    var thumbnailScrollY = $(".carousel").find('[class=thumb_scroll_y]');
    var thumbnailScrollX = $(".carousel").find('[class=thumb_scroll_x]');

    if(thumbnailScrollY){
      $('.thumb_scroll_y').on('slid.bs.carousel', function(){
        var heightEstimate = -1 * $(".thumb_scroll_y .carousel-indicators li:first").position().top + $(".thumb_scroll_y .carousel-indicators li:last").position().top + $(".thumb_scroll_y .carousel-indicators li:last").height();
        var newIndicatorPositionY = $(".thumb_scroll_y .carousel-indicators li.active").position().top + $(".thumb_scroll_y .carousel-indicators li.active").height() / 1;
        var toScrollY = newIndicatorPositionY + indicatorPositionY;
        var adjustedScrollY = toScrollY - ($(".thumb_scroll_y .carousel-indicators").height() / 2);
        if (adjustedScrollY < 0)
          adjustedScrollY = 0;
        if (adjustedScrollY > heightEstimate - $(".thumb_scroll_y .carousel-indicators").height())
          adjustedScrollY = heightEstimate - $(".thumb_scroll_y .carousel-indicators").height();
        $('.thumb_scroll_y .carousel-indicators').animate({ scrollTop: adjustedScrollY }, 800);
          indicatorPositionY = adjustedScrollY;
      });
    } if(thumbnailScrollX){
      $('.thumb_scroll_x').on('slid.bs.carousel', function(){
        var widthEstimate = -1 * $(".thumb_scroll_x .carousel-indicators li:first").position().left + $(".thumb_scroll_x .carousel-indicators li:last").position().left + $(".thumb_scroll_x .carousel-indicators li:last").width();
        var newIndicatorPositionX = $(".thumb_scroll_x .carousel-indicators li.active").position().left + $(".thumb_scroll_x .carousel-indicators li.active").width() / 1;
        var toScrollX = newIndicatorPositionX + indicatorPositionX;
        var adjustedScrollX = toScrollX - ($(".thumb_scroll_x .carousel-indicators").width() / 1);
        if (adjustedScrollX < 0)
          adjustedScrollX = 0;
        if (adjustedScrollX > widthEstimate - $(".thumb_scroll_x .carousel-indicators").width())
          adjustedScrollX = widthEstimate - $(".thumb_scroll_x .carousel-indicators").width();
        $('.thumb_scroll_x .carousel-indicators').animate({ scrollLeft: adjustedScrollX }, 800);
          indicatorPositionX = adjustedScrollX;
      });
    }
    /*--------------------------------------------------
       SELF HOSTED VIDEO PAUSING ON SLIDE
    --------------------------------------------------*/
    //It will work on .pauseVideo class only
    $(".selfPauseVids").on('slide.bs.carousel', function () {
      $("video").each(function(){
        this.pause();
      });
    });
    /*--------------------------------------------------
       SELF HOSTED VIDEO PAUSING ON MODAL CLOSE
    --------------------------------------------------*/
    //It will work on .pauseVideoM class only
    $(".selfPauseVidm").on('hide.bs.modal', function () {
      $("video").each(function(){
        this.pause();
      });
    });
    /*--------------------------------------------------
        YOUTUBE, VIMEO VIDEO PAUSING ON SLIDE
    --------------------------------------------------*/
    //It will work on .onlinePauseVideo class only
    $(".onlinePauseVideos").on('slide.bs.carousel', function (e) {
      var $vidiFrames = $(e.target).find("iframe");
      $vidiFrames.each(function(index, iframe){
        $(iframe).attr("src", $(iframe).attr("src"));
      });
    });
    /*--------------------------------------------------
        YOUTUBE, VIMEO VIDEO PAUSING ON MODAL CLOSE
    --------------------------------------------------*/
    //It will work on .pauseVids class only
    $(".onlinePauseVideom").on('hide.bs.modal', function (e) {
      var $vidiFrames = $(e.target).find("iframe");
      $vidiFrames.each(function(index, iframe){
        $(iframe).attr("src", $(iframe).attr("src"));
      });
    });
	
    /*-----------------------------------------------------------------*/
    /* MOBILE SWIPE
    /*-----------------------------------------------------------------*/
    //Enable swiping...
      $(".carousel").swipe({
        swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
          if (direction == 'left') $(this).carousel('next');
          if (direction == 'right') $(this).carousel('prev');
        },
        allowPageScroll:"vertical",
        //Default is 75px, set to 0 for demo so any distance triggers swipe
        threshold: 0
      });

})(jQuery);