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
	
	$.ajax({
        url: url,
        method: 'GET',
        data: dataToSend,
        dataType: 'JSON'
    }).done(function(data, textStatus, jqXHR){
    	// Add publications
        container.append(data.html);
		// Set the video library
		plyr.setup();
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(errorThrown);
        toastr.error(errorThrown)
    });
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
