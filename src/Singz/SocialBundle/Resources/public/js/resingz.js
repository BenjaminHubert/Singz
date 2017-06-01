/**
 * Resingz a publication
 * @param path
 */
function resingz(path, idPub) {
    toastr.success('Publication resingzée sur votre profil !', '', {positionClass: "toast-top-center"});
    $("#nbresingz-"+idPub).text(parseInt($("#nbresingz-"+idPub).text())+1);
    $("#nbresingz-"+idPub).parent().show();

    $.ajax({
        type: "POST",
        url: path,
        dataType: "json",
        success: function (response) {

        }
    });
}