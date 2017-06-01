/**
 * Resingz a publication
 * @param path
 */
function resingz(path, idPub) {

    toastr.success('Publication resingzée sur votre profil !', '', {positionClass: "toast-top-center"});
    $("#nbresingz-"+idPub).text(parseInt($("#nbresingz-"+idPub).text())+1);
    $("#nbresingz-"+idPub).parent().show();

    $.ajax({
        url: path,
        method: 'POST'
    }).done(function(data, textStatus, jqXHR){

    }).fail(function(jqXHR, textStatus, errorThrown){
        toastr.error('Erreur lors du resingzage :(', '', {positionClass: "toast-top-center"});
    });
}