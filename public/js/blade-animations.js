document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.add('page-ready');

    const selectors = [
        'main > *',
        '.main-content > *',
        '.dashboard-container > *',
        '.table-container',
        '.toolbar',
        '.content-title',
        '.card',
        '.login-container',
        '.hero-content',
        '.modal-content'
    ];

    const seen = new Set();
    const elements = [];

    selectors.forEach(function (selector) {
        document.querySelectorAll(selector).forEach(function (el) {
            if (!seen.has(el)) {
                seen.add(el);
                elements.push(el);
            }
        });
    });

    elements.forEach(function (el, index) {
        el.classList.add('anim-enter');
        el.style.setProperty('--anim-delay', Math.min(index * 45, 450) + 'ms');
        requestAnimationFrame(function () {
            el.classList.add('anim-in');
        });
    });
});
