document.addEventListener('DOMContentLoaded', function () {
    const partnersSliderEl = document.querySelector('.another-partners-slider');
    
    if (partnersSliderEl) {
        new Swiper('.another-partners-slider', {
            loop: true,
            slidesPerView: 2,
            spaceBetween: 30,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 40 },
                768: { slidesPerView: 4, spaceBetween: 50 },
                1024: { slidesPerView: 5, spaceBetween: 60 },
            },
        });
    }
});