$(function () {
    $('.delete-confirm').on('click', function (e) {
        if (!confirm('Удалить?')) {
            e.preventDefault();
        }
    });
});
