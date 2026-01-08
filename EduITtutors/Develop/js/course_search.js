
document.addEventListener('DOMContentLoaded', function () {
    // Get all gallery links
    const galleryLinks = document.querySelectorAll('.gallery-link');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-image');
    const closeBtn = document.querySelector('.close');

    // Open lightbox when gallery item clicked
    galleryLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            lightboxImg.src = this.getAttribute('data-image');
            lightboxImg.alt = this.querySelector('img').alt;
            lightbox.style.display = 'flex';
        });
    });

    // Close lightbox
    closeBtn.addEventListener('click', function () {
        lightbox.style.display = 'none';
    });

    // Close when clicking outside image
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) {
            lightbox.style.display = 'none';
        }
    });

    // Close with ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            lightbox.style.display = 'none';
        }
    });
});
