$(document).ready(function() {

    $('input.entity-image').change(function () {readURL(this)});
});

function readURL(input) {
    let url = input.value;
    let ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

    if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let $img = $(input).parents('.image-bulk-container').find('img.image-view');
            $img.attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0])
    }
}
