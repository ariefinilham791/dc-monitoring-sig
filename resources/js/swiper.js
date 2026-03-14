import Swiper from "swiper";
import {
    Navigation,
    Pagination,
    Autoplay,
    EffectFade,
} from "swiper";

new Swiper(".default-swiper", {
    modules: [Autoplay],
    loop: true,
    spaceBetween: 0,
    autoplay: {delay: 5000},
    breakpoints: {576: {slidesPerView: 1.2}, 768: {slidesPerView: 1}},
    roundLengths: true
})

new Swiper(".testimonial-swiper", {
    modules: [Autoplay, Navigation],
    loop: true,
    spaceBetween: 24,
    autoplay: {delay: 5000},
    breakpoints: {576: {slidesPerView: 1}, 768: {slidesPerView: 2}},
    roundLengths: true,
    navigation: {nextEl: ".swiper-custom-next", prevEl: ".swiper-custom-prev"}
})

new Swiper(".testimonial-swiper-2", {
    modules: [Autoplay, Navigation],
    loop: true,
    spaceBetween: 24,
    autoplay: {delay: 5000},
    breakpoints: {576: {slidesPerView: 1}, 768: {slidesPerView: 1}},
    roundLengths: true,
    navigation: {nextEl: ".swiper-custom-next", prevEl: ".swiper-custom-prev"}
})

new Swiper(".effect-fade-swiper", {
    modules: [Navigation, Autoplay, EffectFade],
    slidesPerView: 1,
    effect: 'fade',
    spaceBetween: 0,
    loop: true,
    roundLengths: true,
    autoplay: {delay: 5000},
    navigation: {
        nextEl: ".swiper-custom-next", prevEl: ".swiper-custom-prev"
    }
})

new Swiper(".auth-swiper", {
        modules: [Pagination, Autoplay],
        slidesPerView: 1,
        loop: true,
        spaceBetween: 0,
        autoplay: {delay: 5000},
        breakpoints: {576: {slidesPerView: 1.2}, 768: {slidesPerView: 1}},
        roundLengths: true,
        pagination: {
            el: ".swiper-pagination", dynamicBullets: true
        }
    }
);


new Swiper(".demo-swiper", {
    modules: [Autoplay, Pagination],
    slidesPerView: 1,
    loop: true,
    spaceBetween: 0,
    autoplay: {delay: 5000},
    roundLengths: true,
    pagination: {el: ".swiper-pagination", dynamicBullets: true}
})
