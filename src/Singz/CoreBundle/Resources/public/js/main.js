(function() {

    $(document).on('scroll', function() {
        if($(this).scrollTop()>=$('.grid-wrap').first().position().top){
            $('.lighter').first().dequeue();
            $('.lighter').first().addClass("fixed-nav");
            $('#grid-gallery').addClass("grid-fixed-nav");
        }else if($('#grid-gallery').hasClass("grid-fixed-nav")){

            $('.lighter').first().addClass("remove-fixed-nav");
            $('.lighter').first().delay(100).queue(function() {
                $('.lighter').first().removeClass("remove-fixed-nav");
                $('.lighter').first().removeClass("fixed-nav");
                $('#grid-gallery').removeClass("grid-fixed-nav");
                $('.lighter').first().dequeue();
            });
        }
    })

})();
