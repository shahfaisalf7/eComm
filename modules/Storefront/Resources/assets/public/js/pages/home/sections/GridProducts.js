import { Grid, Navigation, Pagination } from "swiper/modules";
import ProductTabsMixin from "../../../mixins/ProductTabsMixin";
import "../../../components/ProductCard";

Alpine.data("GridProducts", (tabs) => ({
    ...ProductTabsMixin(tabs),

    init() {
        this.changeTab(0);
    },

    url(tabIndex) {
        return route("storefront.product_grid.index", {
            tabNumber: tabIndex + 1,
        });
    },

    selector() {
        return ".grid-products";
    },

    swiperOptions() {
        return {
            modules: [Grid, Navigation, Pagination],
            slidesPerView: 2,
            grid: {
                rows: 2,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
                clickable: true,
            },

            breakpoints: {
                576: {
                    slidesPerView: 3,
                    grid: {
                        rows: 2,
                    },
                },
                830: {
                    slidesPerView: 4,
                    grid: {
                        rows: 2,
                    },
                },
                991: {
                    slidesPerView: 5,
                    grid: {
                        rows: 2,
                    },
                },
                1200: {
                    slidesPerView: 6,
                    grid: {
                        rows: 2,
                    },
                },
                1400: {
                    slidesPerView: 7,
                    grid: {
                        rows: 2,
                    },
                },
                1760: {
                    slidesPerView: 8,
                    grid: {
                        rows: 2,
                    },
                },
            },
        };
    },
}));
