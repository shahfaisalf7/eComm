import Swiper from "swiper";
import { Navigation, Pagination } from "swiper/modules";
import "../../components/ProductCard";
import "../../components/ProductRating";
import "../../components/LandscapeProducts";

Alpine.data("Compare", ({ products, attributes }) => ({
    products,
    attributes,

    get hasAnyProduct() {
        return Object.keys(this.products).length !== 0;
    },

    badgeClass(product) {
        if (product.is_in_stock) {
            return "badge-success";
        }

        return "badge-danger";
    },

    hasAttribute(product, attribute) {
        for (let productAttribute of product.attributes) {
            if (productAttribute.name === attribute.name) {
                return true;
            }
        }
    },

    attributeValues(product, attribute) {
        for (let productAttribute of product.attributes) {
            if (productAttribute.name === attribute.name) {
                return productAttribute.values
                    .map((productAttributeValue) => {
                        return productAttributeValue.value;
                    })
                    .join(", ");
            }
        }
    },

    removeItem() {
        delete this.products[this.product.id];

        this.$store.state.removeFromCompareList(this.product.id);
    },
}));
