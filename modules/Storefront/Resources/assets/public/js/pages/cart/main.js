import "../../components/CartItem";
import "../../components/LandscapeProducts";

Alpine.data("Cart", () => ({
    loadingOrderSummary: false,
    shippingMethodName: null,

    get cartIsEmpty() {
        return this.$store.state.cartIsEmpty;
    },

    clearCart() {
        this.$store.state.clearCart();

        axios
            .delete(route("cart.clear"))
            .then(({ data }) => {
                this.$store.state.updateCart(data);
            })
            .catch((error) => {
                notify(error.response.data.message);
            });
    },
}));
