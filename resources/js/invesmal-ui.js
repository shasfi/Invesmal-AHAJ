document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-animate]').forEach((el, i) => {
        el.style.animationDelay = `${Math.min(i * 0.06, 0.4)}s`;
        el.classList.add('invesmal-animate-in');
    });
});