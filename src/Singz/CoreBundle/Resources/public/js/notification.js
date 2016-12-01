(function() {
    var bttn = document.getElementById( 'notification-trigger' );

    // make sure..
    bttn.disabled = false;

    bttn.addEventListener( 'click', function() {

        $.notify.addStyle('notification', {
            html:   "<div>" +
                        "<div class='ns-thumb'>" +
                            "<img src='img/user1.jpg'/>" +
                        "</div>" +
                        "<div class='ns-content'>" +
                            "<p>jfjehuzhfuizehf<span data-notify-text/></p>" +
                        "</div>" +
                        "<div class='ns-close'></div>" +
                    "</div>"
        });

        $.notify('hello !!', {
            style: 'notification',
            autoHide: true,
            clickToHide: false,
            autoHideDelay: 5000
        });



        // simulate loading (for demo purposes only)
        //classie.add( bttn, 'active' );
        // setTimeout( function() {
        //
        //     classie.remove( bttn, 'active' );
        //
        //     // create the notification
        //     var notification = new NotificationFx({
        //         message : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">Zoe Moulder</a> accepted your invitation.</p></div>',
        //         layout : 'other',
        //         ttl : 6000,
        //         effect : 'thumbslider',
        //         type : 'notice', // notice, warning, error or success
        //         onClose : function() {
        //             bttn.disabled = false;
        //         }
        //     });
        //
        //     // show the notification
        //     notification.show();
        //
        // }, 1200 );
        //
        // // disable the button (for demo purposes only)
        // this.disabled = false;
    } );

    $(document).on('click', '.notifyjs-notification-base .ns-close', function() {
        //programmatically trigger propogating hide event
        $(this).parent().addClass("ns-hide");
        $(this).trigger('notify-hide');
    });
})();
