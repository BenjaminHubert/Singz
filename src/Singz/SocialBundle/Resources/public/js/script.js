$(function(){
	$(document).on('click', '.btn-love', function(){
		var idPub = $(this).attr('data-publication-id');
		var idUser = $(this).attr('data-user-id');
		var isLoved = $(this).attr('data-is-loved');
		if(isLoved == 'true'){
			$(this).html('<i class="fa fa-heart-o" aria-hidden="true"></i>');
			$(this).children('.fa-heart-o').removeClass('animate-like');
			$(this).attr('data-is-loved', 'false')
		}else{
			$(this).html('<i class="fa fa-heart" aria-hidden="true"></i>');
			$(this).children('.fa-heart').addClass('animate-like');
			$(this).attr('data-is-loved', 'true')
		}
	    $.ajax({
	        type: "POST",
	        url: path,
	        data: {
	            idPub: idPub, idUser: idUser
	        },
	        dataType: "json",
	        success: function (response) {
	            nbLove = response.loves.length;
	            didLove = response.didLove;
	            
	            if(nbLove != 1) {
	                $("#nbloves-"+idPub).text(nbLove);
	                $(".nbloves-"+idPub).each(function () {
	                    $(this).text(nbLove);
	                });
	            } else {
	                $("#nbloves-"+idPub).text(nbLove);
	                $(".nbloves-"+idPub).each(function () {
	                    $(this).text(nbLove);
	                });
	            }
	        }
	    });
	})
});

/**
 * Follow & Unfollow an user
 * @param path
 * @param idLeader
 * @param idUser
 */
function followUser(path, idLeader, idUser) {
    $.ajax({
        type: "POST",
        url: path,
        data: {
            idLeader: idLeader, idUser: idUser
        },
        dataType: "json",
        success: function (response) {
            console.log("Vous suivez l'utilisateur "+idLeader);
        }
    });
}