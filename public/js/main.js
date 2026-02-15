/* ========================================
   THALYE D'ORIAM — JavaScript
   ======================================== */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Header scroll ----
    const header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', function () {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });
        // État initial
        header.classList.toggle('scrolled', window.scrollY > 50);
    }

    // ---- Theme toggle (dark/light) ----
    var themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            if (next === 'light') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', '');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // ---- Menu mobile ----
    const menuToggle = document.getElementById('menu-toggle');
    const siteNav = document.getElementById('site-nav');
    if (menuToggle && siteNav) {
        menuToggle.addEventListener('click', function () {
            menuToggle.classList.toggle('open');
            siteNav.classList.toggle('open');
        });
        // Fermer le menu au clic sur un lien
        siteNav.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                menuToggle.classList.remove('open');
                siteNav.classList.remove('open');
            });
        });
    }

    // ---- Lightbox ----
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxInfo = document.getElementById('lightbox-info');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');

    let lightboxItems = [];
    let lightboxIndex = 0;

    function collectLightboxItems() {
        lightboxItems = Array.from(document.querySelectorAll('[data-lightbox]:not([style*="display: none"])'));
    }

    function openLightbox(index) {
        collectLightboxItems();
        if (index < 0 || index >= lightboxItems.length) return;
        lightboxIndex = index;
        const item = lightboxItems[index];
        lightboxImg.src = item.dataset.src;
        lightboxImg.alt = item.dataset.title || '';
        lightboxInfo.innerHTML = '';
        if (item.dataset.title) {
            lightboxInfo.innerHTML += '<h3>' + escapeHtml(item.dataset.title) + '</h3>';
        }
        if (item.dataset.info) {
            lightboxInfo.innerHTML += '<p>' + escapeHtml(item.dataset.info) + '</p>';
        }
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        lightboxImg.src = '';
    }

    function showPrev() {
        if (lightboxIndex > 0) openLightbox(lightboxIndex - 1);
    }

    function showNext() {
        if (lightboxIndex < lightboxItems.length - 1) openLightbox(lightboxIndex + 1);
    }

    // Clic sur les items de la galerie
    document.addEventListener('click', function (e) {
        const item = e.target.closest('[data-lightbox]');
        if (item) {
            e.preventDefault();
            collectLightboxItems();
            var idx = lightboxItems.indexOf(item);
            if (idx >= 0) openLightbox(idx);
        }
    });

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxPrev) lightboxPrev.addEventListener('click', showPrev);
    if (lightboxNext) lightboxNext.addEventListener('click', showNext);

    // Fermer avec Escape et naviguer avec les flèches
    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });

    // Fermer en cliquant sur le fond
    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // ---- Filtre galerie ----
    document.querySelectorAll('.filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var filter = this.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            document.querySelectorAll('.gallery-item').forEach(function (item) {
                if (filter === 'all' || item.dataset.technique === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // ---- Image preview admin ----
    document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            var preview = this.parentElement.querySelector('.image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'image-preview';
                this.parentElement.appendChild(preview);
            }
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu">';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ---- Utilitaire ----
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
