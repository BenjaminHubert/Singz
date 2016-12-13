/**
 * Love & Unlove a publication
 * @param path
 * @param idPub
 * @param idUser
 */
function lovethis(path, idPub, idUser) {
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
                $("#nbloves-"+idPub).text(nbLove + ' loves');
            } else {
                $("#nbloves-"+idPub).text(nbLove + ' love');
            }

            if(didLove){
                $("#love-"+idPub).text('Je love !');
            } else {
                $("#love-"+idPub).text('Je ne love plus');
            }
        }
    });
}

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