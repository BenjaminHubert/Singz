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
}, 'shift');

$('form').submit(function(e){
	// Prevent default behavior
	e.preventDefault();
	// Get form data
	$data = $(this).serialize()
	// Empty the textarea field
	$(this).find('textarea').val('');
	// Launch the AJAX Request
	$.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $data,
    }).done(function(data, textStatus, jqXHR){
    	
    }).fail(function(jqXHR, textStatus, errorThrown){
    	console.error(jqXHR);
    });
});
