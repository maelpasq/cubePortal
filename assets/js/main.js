document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.flash').forEach((el) => {
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transition = 'opacity 0.4s ease';
        }, 5000);
    });
});
