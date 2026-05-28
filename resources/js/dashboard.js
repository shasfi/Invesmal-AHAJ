// Invesmal — Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Logout
    var logoutBtn = document.getElementById('logout-button');
    var logoutForm = document.getElementById('logout-form');
    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            logoutForm.submit();
        });
    }

    // Sidebar
    var sidebar = document.getElementById('sidebar');
    if (sidebar) {
        var sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
            });
        }

        var hamburgerBtn = document.querySelector('.hamburger-btn');
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                toggleScrim(sidebar.classList.contains('open'));
            });
        }

        var scrim = document.querySelector('.sidebar-scrim');
        if (scrim) {
            scrim.addEventListener('click', function () {
                sidebar.classList.remove('open');
                toggleScrim(false);
            });
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('open');
                toggleScrim(false);
            }
        });
    }

    function toggleScrim(show) {
        var scrimEl = document.querySelector('.sidebar-scrim');
        if (scrimEl) {
            scrimEl.classList.toggle('active', show);
        }
    }

    // Scroll Reveal Animation
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('[data-reveal]').forEach(function (el) {
        revealObserver.observe(el);
    });

    // Card mouse-follow glow effect
    document.querySelectorAll('.startup-card').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            var rect = card.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            card.style.setProperty('--mouse-x', x + '%');
            card.style.setProperty('--mouse-y', y + '%');
        });
    });

    // Progress bar animation on scroll
    var progressObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                var bar = entry.target.querySelector('.card-progress-bar, .featured-progress-bar, .funding-panel-progress');
                if (bar) {
                    var width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(function () {
                        bar.style.width = width;
                    }, 100);
                }
                progressObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.card-progress-wrap, .featured-progress-wrap, .funding-panel-progress-wrap').forEach(function (el) {
        progressObserver.observe(el);
    });
});
