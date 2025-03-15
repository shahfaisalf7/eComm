import { Pagination } from "swiper/modules";
import ProductTabsMixin from "../../../mixins/ProductTabsMixin";
import "../../../components/ProductCard";

Alpine.data("FeaturedCategories", (tabs) => ({
    ...ProductTabsMixin(tabs),

    init() {
        this.changeTab(0);
    },

    url(tabIndex) {
        return route("storefront.featured_category_products.index", {
            categoryNumber: tabIndex + 1,
        });
    },

    selector() {
        return ".featured-category-products";
    },

    swiperOptions() {
        return {
            modules: [Pagination],
            slidesPerView: 2,
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
                clickable: true,
            },
            breakpoints: {
                576: {
                    slidesPerView: 3,
                },
                830: {
                    slidesPerView: 4,
                },
                991: {
                    slidesPerView: 5,
                },
                1200: {
                    slidesPerView: 6,
                },
                1400: {
                    slidesPerView: 7,
                },
                1760: {
                    slidesPerView: 8,
                },
            },
        };
    },
}));
