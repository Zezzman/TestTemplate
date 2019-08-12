$(document).ready(function () {
    // disable form submit
    $('.no-submit').submit(false);
    // enable form submit on enter key
    $('.form-enter').keypress(function (event) {
        if (event.which == 13) {
            $(this).submit();
        }
    });
    // enable tooltips on data-toggle=tooltip
    $('[data-toggle="tooltip"]').tooltip();
});