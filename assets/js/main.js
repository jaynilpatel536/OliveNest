function toggleMenu() {
    let nav = document.getElementById("fullscreen-nav");

    if (nav.style.top === "0%") {
        nav.style.top = "-100%";
    } else {
        nav.style.top = "0%";
    }
}
// ================= LIGHTBOX ==================
const galleryItems = document.querySelectorAll('.gallery-item');
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');

galleryItems.forEach(item => {
    item.addEventListener('click', function() {
        lightbox.style.display = 'flex';
        lightboxImg.src = this.src;
    });
});

function closeLightbox() {
    lightbox.style.display = 'none';
}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
