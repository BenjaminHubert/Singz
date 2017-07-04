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