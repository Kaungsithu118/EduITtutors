document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".slider", {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        effect: "fade", // Optional for smooth transitions
        fadeEffect: {
            crossFade: true,
        },
    });
});



document.addEventListener("DOMContentLoaded", function () {
    // Counter animation
    const counters = document.querySelectorAll(".milestone_counter");

    const startCounting = (counter) => {
        const endValue = parseInt(counter.getAttribute("data-end-value"));
        const signAfter = counter.getAttribute("data-sign-after") || "";
        let current = 0;
        const speed = 50;

        const updateCounter = () => {
            const increment = Math.ceil(endValue / speed);
            if (current < endValue) {
                current += increment;
                if (current > endValue) current = endValue;
                counter.textContent = current + signAfter;
                setTimeout(updateCounter, 30);
            } else {
                counter.textContent = endValue + signAfter;
            }
        };

        updateCounter();
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                startCounting(counter);
                observer.unobserve(counter);
            }
        });
    }, {
        threshold: 0.7
    });

    counters.forEach(counter => {
        observer.observe(counter);
    });

    // Swiper for testimonials
    const testimonialSwiper = new Swiper(".testimonial-swiper", {
        loop: true,
        spaceBetween: 30,
        slidesPerView: 3,
        slidesPerGroup: 1,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        speed: 600,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
});
