import ProductMixin from "../mixins/ProductMixin";
import "./ProductRating";

Alpine.data("ProductCard", (product) => ({
    ...ProductMixin(product),

    get inWishlist() {
        return this.$store.state.inWishlist(this.product.id);
    },

    get inCompareList() {
        return this.$store.state.inCompareList(this.product.id);
    },
}));
