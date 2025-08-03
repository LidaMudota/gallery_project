document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-confirm').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm('Удалить?')) {
                e.preventDefault();
            }
        });
    });
});