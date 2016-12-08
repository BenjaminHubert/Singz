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
                $("#nb_loves").text(nbLove + ' loves');
            } else {
                $("#nb_loves").text(nbLove + ' love');
            }

            if(didLove){
                $("#love").text('Je love !');
            } else {
                $("#love").text('Je ne love plus');
            }
        }
    });
}