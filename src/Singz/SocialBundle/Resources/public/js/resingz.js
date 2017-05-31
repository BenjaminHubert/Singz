/**
 * Reseingz a publication
 * @param path
 */
function resingz(path) {
    $.ajax({
        type: "POST",
        url: path,
        dataType: "json",
        success: function (response) {
            toastr.success('Publication resingzée sur votre profil !', '', {positionClass: "toast-top-center"});
        }
    });
}