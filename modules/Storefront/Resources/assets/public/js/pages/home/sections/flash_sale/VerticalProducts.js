import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import { chunk } from "lodash";
import "../../../../components/ProductCard";

Alpine.data("VerticalProducts", (columnNumber) => ({
    chunk,
    products: [],

    get hasAnyProduct() {
        return this.products.length !== 0;
    },

    init() {
        this.fetchProducts();
    },

    async fetchProducts() {
        const response = await axios.get(
            route("storefront.vertical_products.index", {
                columnNumber,
            })
        );

        this.products = response.data;

        this.$nextTick(() => {
            new Swiper(this.$refs.verticalProducts, this.swiperOptions());
        });
    },

    swiperOptions() {
        return {
            modules: [Navigation],
            slidesPerView: 1,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        };
    },
}));
