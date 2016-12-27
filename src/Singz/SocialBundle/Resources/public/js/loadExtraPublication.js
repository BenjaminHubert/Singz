$(document).ready(function(){
	$('#sz_video_003_4_col').on('show.bs.modal', function (e) {
		//add loading
		$('.extra-publication').html('');
		$('.loading-comments').slideDown('fast', function(){
			var idPublication = $('.carousel-inner .item.active').attr('data-big-publication');
    		$.ajax({
    	        url: pathExtraPublication,
    	        method: 'POST',
    	        data: { 'idPublication': idPublication }
    	    }).done(function(data, textStatus, jqXHR){
    		    $('.modal-content .extra-publication').html(data);
    	    }).fail(function(jqXHR, textStatus, errorThrown){
    		    $('.modal-content .extra-publication').html(textStatus);
    	    }).always(function(){
    	    	$('.loading-comments').slideUp('fast');
	    	});
		});
	})
});