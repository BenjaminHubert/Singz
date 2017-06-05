$(document).ready(function(){
	$('#sz_video_003_4_col').on('show.bs.modal', function (e) {
		// Reset content
		$('.extra-publication').html('');
		// Add loading
		$('.loading-comments').slideDown('fast', function(){
			var idPublication = $('.carousel-inner .item.active').attr('data-big-publication');
    		$.ajax({
    	        url: pathExtraPublication,
    	        method: 'POST',
    	        data: { 'idPublication': idPublication },
    	        dataType: 'JSON'
    	    }).done(function(data, textStatus, jqXHR){
    	    	$('.modal-content .extra-publication').html(data.html);
    	    }).fail(function(jqXHR, textStatus, errorThrown){
    	    	// Inject the error into the html
    	    	$('.error-template p.error-text').text(jqXHR.responseJSON.error);
    	    	// Get the HTML template
    	    	var htmlTemplate = $('.error-template').html();
    	    	// Display the error
    		    $('.modal-content .extra-publication').html(htmlTemplate);
    	    }).always(function(){
    	    	$('.loading-comments').slideUp('fast');
	    	});
		});
	})
});
