var loadingPublications = false;

function getPublications(url, container, filter, offset, limit){
	var dataToSend = {};
	if(filter != 'undefined'){
		dataToSend['filter'] = filter;
	}
	if(offset != 'undefined'){
		dataToSend['offset'] = offset;
	}
	if(limit != 'undefined'){
		dataToSend['limit'] = limit;
	}
	
	if(loadingPublications == false){
		loadingPublications = true;
		$.ajax({
	        url: url,
	        method: 'GET',
	        data: dataToSend,
	        dataType: 'JSON'
	    }).done(function(data, textStatus, jqXHR){
	    	if(data.html == ''){
	    		$('nav.loading-publications').hide();
	    		$('.no-publication').show();
	    		return;
	    	}
	    	// Add publications
	        container.append(data.html);
			// Set the video library
			plyr.setup();
			loadingPublications = false;
	    }).fail(function(jqXHR, textStatus, errorThrown){
	        console.log(errorThrown);
	        toastr.error(errorThrown);
	        loadingPublications = false;
	    }).always(function(){
	    	
	    });
	}
}

$(function(){
	$('.publications').on('show.bs.modal', '.modal-publication', function(){
		var $publication = $(this);
		var idPublication = $publication.data('id-publication');
		var url = $publication.data('href');
		// Check if the extra publication has been not already loaded
		if($publication.attr('loaded') != 'true'){
			// Get extra publication
    		$.ajax({
    	        url: url,
    	        method: 'GET',
    	        data: { 'idPublication': idPublication },
    	        dataType: 'JSON'
    	    }).done(function(data, textStatus, jqXHR){
    	    	// Add extra publication html
    	    	$publication.find('.modal-content .extra-publication').html(data.html);
    	    	// Set the extra publication as loaded
    	    	$publication.attr('loaded', 'true');
    	    }).fail(function(jqXHR, textStatus, errorThrown){
    	    	$publication.find('.modal-content .extra-publication').text(jqXHR.responseJSON.error);
    	    }).always(function(){
    	    	$publication.find('.loading-comments').slideUp('fast');
	    	});
		}
	})
});
