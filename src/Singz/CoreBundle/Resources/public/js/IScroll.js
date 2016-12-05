var isProcessing = false;
var offset = 5;

$(function() {
    $(window).scroll(function () {
        var wintop = $(window).scrollTop(), docheight = $(document).height(), winheight = $(window).height();

        var scrolltrigger = 0.90;
        if ((wintop / (docheight - winheight)) > scrolltrigger) {
            if (!isProcessing) {
                isProcessing = true;
                console.log("appel ajax, offset="+offset);
                $.ajax({
                    type: "POST",
                    url: path,
                    data: {
                        nbPub: offset
                    },
                    contentType: 'json',
                    success: function (data) {
                        container = $(".browse-list .empty");
                        container.text("{{ block('list') }}");
                        isProcessing = false;
                    }
                });
            }
        }
    });
});