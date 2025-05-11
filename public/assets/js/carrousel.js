document.addEventListener('DOMContentLoaded', function() {
    const carouselInner = document.querySelector('.carousel-inner');
    if (!carouselInner) {
        console.error('Error: .carousel-inner element not found');
        return;
    }

    let currentIndex = 0;
    const items = document.querySelectorAll('.carousel-item');
    const totalItems = items.length;
    const dots = document.querySelectorAll('.carousel-dot');
    let carouselInterval;

    if (totalItems === 0) {
        console.error('Error: No .carousel-item elements found');
        return;
    }

    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }

    function updateItems() {
        items.forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });
    }

    function moveSlide(direction) {
        currentIndex = (currentIndex + direction + totalItems) % totalItems;
        updateCarousel();
    }

    function goToSlide(index) {
        currentIndex = index >= 0 && index < totalItems ? index : 0;
        updateCarousel();
    }

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselInner.style.transform = `translateX(${offset}%)`;
        updateDots();
        updateItems();
    }

    function startCarousel() {
        carouselInterval = setInterval(() => {
            moveSlide(1);
        }, 5000);
    }

    const carouselWrapper = document.querySelector('.carousel-wrapper');
    if (carouselWrapper) {
        carouselWrapper.addEventListener('mouseenter', () => {
            clearInterval(carouselInterval);
        });
        carouselWrapper.addEventListener('mouseleave', () => {
            startCarousel();
        });
    }

    startCarousel();
    window.moveSlide = moveSlide;
    window.goToSlide = goToSlide;
});