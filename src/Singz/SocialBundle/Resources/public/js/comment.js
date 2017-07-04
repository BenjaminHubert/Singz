function reportCommentListener(e, $this){
	if(confirm('Êtes vous sûr?')){
		var url = $this.attr('href');		
		$.ajax({
	        url: url,
	        method: 'POST',
	    }).done(function(data, textStatus, jqXHR){
	    	
	    }).fail(function(jqXHR, textStatus, errorThrown){
	    	console.error(jqXHR);
	    });
	}
	e.preventDefault();
}

function changeStateListener(e, $this){
	if(confirm('Êtes vous sûr?')){
		var url = $this.attr('href');
		// Get thread id
		var thread = $("#comment-thread").data("thread");
		$this.closest('li.comment').hide('fast');
		$.ajax({
			url: url,
			method: 'POST',
		}).done(function(data, textStatus, jqXHR){
			$(".nbcomments-"+thread).each(function () {
				$(this).html(parseInt($(this).html(), 10)-1);
			});
		}).fail(function(jqXHR, textStatus, errorThrown){
			console.error(jqXHR);
		});
	}
	e.preventDefault();
}

function commentSubmit(e, $this, depth){
	// Prevent default behavior
	e.preventDefault();
	// Get form data
	$data = $this.serialize();
	// Check if not empty
	$textareaValue = $this.find('textarea').val().trim();
	if($textareaValue.length == 0){
		toastr["error"]("Votre commentaire est vide");
		return;
	}
	// Get thread id
	var thread = $("#comment-thread").data("thread");
	// Empty the textarea field
	$this.find('textarea').val('');
	// Create  the new comment
	$('#comment-template').find('p').html($textareaValue);
	$('#comment-template').find('.comment-body').addClass('new-comment');
	var htmlTemplate = $('#comment-template').html();
	$('#comment-template').find('.comment-body').removeClass('new-comment');
	if(depth == 'first'){
		$('.post-footer > ul.comments-list').append(htmlTemplate);
	}else if(depth == 'second'){
		$this.before(htmlTemplate);
	}
	// Scroll to the comment
	$('.right-side, .modal-dialog').animate({
		scrollTop: $(".new-comment").last().offset().top
	}, 500);
	$(".new-comment").css('opacity', 0.33);
	// Launch the AJAX Request
	$.ajax({
		url: $this.attr('action'),
		method: 'POST',
		data: $data,
		dataType: 'JSON'
	}).done(function(data, textStatus, jqXHR){
		// Increase the number of comment
		$(".nbcomments-"+thread).each(function () {
			$(this).html(parseInt($(this).html(), 10)+1);
		});
		// Update the comment 
		$(".new-comment").closest('li.comment').html(data.html);
	}).fail(function(jqXHR, textStatus, errorThrown){
		$(".new-comment").last().parent('li.comment').remove()
		toastr["error"]("Une erreur a été rencontrée.")
	});
}

$(function(){
	$(document).on('keypress', 'textarea', function(evt){
		if (evt.keyCode == 13 && !evt.shiftKey) {
	        if (evt.type == "keypress") {
	        	$(this).closest('form').submit();
	    		$(this).blur();
	        }
	        evt.preventDefault();
	    }
	});
	
	$(document).on('submit', 'form.first-depth', function(e){
		commentSubmit(e, $(this), 'first');
	});
	
	$(document).on('submit', 'form.second-depth', function(e){
		commentSubmit(e, $(this), 'second');
	});
	
	$(document).on('click', 'a.change-state', function(e){
		changeStateListener(e, $(this));
	})
	
	$(document).on('click', 'a.report-comment', function(e){
		reportCommentListener(e, $(this));
	})
	
	$(document).on('click', 'a.reply-comment', function(){
		// Display the form
		$(this).closest('.comments-list').find('form').removeClass('hidden');
		// Focus the form
		$(this).closest('.comments-list').find('form').find('textarea').focus();
		// Remove the button
		$(this).remove();
	})
	
	$(document).on('click', '.confirmation-alert', function(){
		return confirm('Êtes-vous sûr?');
	});
	
	$('.publications').on('shown.bs.modal', '.modal-publication', function(){
		var videoHeight = $(this).find('.video').height();
		$(this).find('.extra-publication').height(videoHeight);
	});
	$(document).resize(function(){
		var videoHeight = $('.modal-publication .video').height();
		$('.modal-publication .extra-publication').height(videoHeight);
	})
})

