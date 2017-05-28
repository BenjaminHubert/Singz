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

$('textarea').enterKey(function() {
	$(this).closest('form').submit();
	$(this).blur(); 
}, 'shift');

$('form.first-depth').submit(function(e){
	// Prevent default behavior
	e.preventDefault();
	// Get form data
	$data = $(this).serialize();
	// Empty the textarea field
	$textareaValue = $(this).find('textarea').val();
	$(this).find('textarea').val('');
	// Create  the new comment
	$('#comment-template').find('p').html($textareaValue);
	$('#comment-template').find('.comment-body').addClass('new-comment')
	var htmlTemplate = $('#comment-template').html();
	$('#comment-template').find('.comment-body').removeClass('new-comment');
	$('.post-footer > ul.comments-list').append(htmlTemplate);
	// Scroll to the comment
	$('.right-side, .modal-dialog').animate({
        scrollTop: $(".new-comment").last().offset().top
    }, 500);
	var time = 300;
	$(".new-comment").last().fadeIn(time).fadeOut(time).fadeIn(time).fadeOut(time).fadeIn(time);
	// Launch the AJAX Request
	$.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $data,
    }).done(function(data, textStatus, jqXHR){
    	
    }).fail(function(jqXHR, textStatus, errorThrown){
    	$(".new-comment").last().parent('li.comment').remove()
    });
});

$('form.second-depth').submit(function(e){
	// Prevent default behavior
	e.preventDefault();
	// Get form data
	$data = $(this).serialize();
	// Empty the textarea field
	$textareaValue = $(this).find('textarea').val();
	$(this).find('textarea').val('');
	// Create  the new comment
	$('#comment-template').find('p').html($textareaValue);
	$('#comment-template').find('.comment-body').addClass('new-comment')
	var htmlTemplate = $('#comment-template').html();
	$('#comment-template').find('.comment-body').removeClass('new-comment');
	$(this).before(htmlTemplate);
	// Scroll to the comment
	$('.right-side, .modal-dialog').animate({
        scrollTop: $(".new-comment").last().offset().top
    }, 500);
	var time = 300;
	$(".new-comment").last().fadeIn(time).fadeOut(time).fadeIn(time).fadeOut(time).fadeIn(time);
	// Launch the AJAX Request
	$.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $data,
    }).done(function(data, textStatus, jqXHR){
    	
    }).fail(function(jqXHR, textStatus, errorThrown){
    	console.error(jqXHR);
    	$(".new-comment").last().parent('li.comment').remove()
    });
});
