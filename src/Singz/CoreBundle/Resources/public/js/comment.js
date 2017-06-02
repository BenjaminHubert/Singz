$.fn.enterKey = function (fnc, mod) {
	return this.each(function () {
		$(this).keypress(function (ev) {
			var keycode = (ev.keyCode ? ev.keyCode : ev.which);
			if (keycode == '13' && mod && !ev[mod + 'Key']) {
				fnc.call(this, ev);
				return false;
			}
		})
	})
}

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
		// Update the listener
		$("#comment-no-"+data.idComment+" a.change-state").click(function(e){
			changeStateListener(e, $(this));
		});
		$("#comment-no-"+data.idComment+" a.report-comment").click(function(e){
			reportCommentListener(e, $(this));
		});
	}).fail(function(jqXHR, textStatus, errorThrown){
		$(".new-comment").last().parent('li.comment').remove()
		toastr["error"]("Une erreur a été rencontrée.")
	});
}

$('textarea').enterKey(function() {
	$(this).closest('form').submit();
	$(this).blur();
}, 'shift');

$('form.first-depth').submit(function(e){
	commentSubmit(e, $(this), 'first');
});

$('form.second-depth').submit(function(e){
	commentSubmit(e, $(this), 'second');
});

$('a.change-state').click(function(e){
	changeStateListener(e, $(this));
})

$('a.report-comment').click(function(e){
	reportCommentListener(e, $(this));
})

$('a.reply-comment').click(function(){
	// Display the form
	$(this).closest('.comments-list').find('form').removeClass('hidden');
	// Focus the form
	$(this).closest('.comments-list').find('form').find('textarea').focus();
	// Remove the button
	$(this).remove();
})


