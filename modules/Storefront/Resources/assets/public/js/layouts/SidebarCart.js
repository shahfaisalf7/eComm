import "../components/CartItem";

Alpine.data("SidebarCart", () => ({
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
