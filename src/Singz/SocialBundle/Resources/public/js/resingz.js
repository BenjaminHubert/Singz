$(function(){
	/**
	 * Resingz a publication
	 * @param path
	 */
	$(document).on('click', '.btn-resingz', function(){
		// Get data
		var $btn = $(this);
		var idPub = $btn.data('id-publication');
		var path = $btn.data('href');
		var isResingz = $btn.hasClass('isResingz');
		// Add rotation to the icon
		$btn.find('i.fa').addClass('fa-spin');
	    // Update the database
	    $.ajax({
	        url: path,
	        method: 'POST'
	    }).done(function(data, textStatus, jqXHR){
	    	// Increase/decrease the number of resingz
	    	var nbResingz = parseInt($("#nbresingz-"+idPub).text());
	    	if(isResingz){
	    		nbResingz--;
	    	}else{
	    		nbResingz++;
	    	}
	        $("#nbresingz-"+idPub).text(nbResingz);
	        // Display the number of resingz
	        $("#nbresingz-"+idPub).parent().show();
	        if(nbResingz == 0){
	        	$("#nbresingz-"+idPub).parent().hide();
	        }
	    	// Add rotation to the icon
	    	$btn.find('i.fa').removeClass('fa-spin');
	    	// Update the button
	    	var text = (isResingz) ? 'Resingzer' : 'Annuler';
	    	$btn.find('.has-resingz').text(text);
	    	// Update the status
	    	$btn.toggleClass('isResingz'); 
	    }).fail(function(jqXHR, textStatus, errorThrown){
	        toastr.error('Erreur lors du resingzage :(');
	    });
	});
})
