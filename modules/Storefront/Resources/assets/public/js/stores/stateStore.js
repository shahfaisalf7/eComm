Alpine.store("state", {
    cart: { ...FleetCart.cart },
    wishlist: [...FleetCart.wishlist],
    compareList: [...FleetCart.compareList],
    coupon: {},
    loading: false,

    get cartItems() {
        return this.cart.items;
    },

    get cartQuantity() {
        return Object.values(this.cartItems).reduce(
            (total, item) => total + item.qty,
            0
        );
    },

    get cartIsEmpty() {
        return Object.keys(this.cart.items).length === 0;
    },

    get shippingCost() {
        if (this.cart.coupon.free_shipping) {
            return 0;
        }
        return this.cart.shippingCost.inCurrentCurrency.amount;
    },

    get taxTotal() {
        return Object.values(this.cart.taxes).reduce((accumulator, tax) => {
            return accumulator + tax.amount.inCurrentCurrency.amount;
        }, 0);
    },

    get cartSubTotal() {
        return Object.values(this.cartItems).reduce((accumulator, cartItem) => {
            return (
                accumulator +
                cartItem.qty * cartItem.unitPrice.inCurrentCurrency.amount
            );
        }, 0);
    },

    get cartTotal() {
        return (
            this.cartSubTotal -
            this.couponValue +
            this.taxTotal +
            this.shippingCost
        );
    },

    get compareIsEmpty() {
        return this.compareList.length === 0;
    },

    get compareCount() {
        return this.compareList.length;
    },

    get wishlistCount() {
        return this.wishlist.length;
    },

    get hasCoupon() {
        return Boolean(this.cart.coupon.code);
    },

    get couponValue() {
        return this.cart.coupon?.value?.inCurrentCurrency?.amount ?? 0;
    },

    updateCart(cart) {
        this.cart = { ...cart };

        this.setCoupon(cart);
    },

    updateCartItemQty({ id, qty }) {
        this.cart.items[id].qty = qty;
    },

    removeCartItem(id) {
        delete this.cart.items[id];
    },

    clearCart() {
        this.cart.items = {};
    },

    setCoupon(cart) {
        if (cart.coupon.code) {
            this.cart.coupon = cart.coupon;
        }
    },

    inWishlist(id) {
        return this.wishlist.includes(id);
    },

    syncWishlist(id) {
        if (this.inWishlist(id)) {
            this.removeFromWishlist(id);

            return;
        }

        this.addToWishlist(id);
    },

    async addToWishlist(id) {
        if (FleetCart.loggedIn) {
            this.wishlist.push(id);

            await axios.post(route("account.wishlist.products.store"), {
                productId: id,
            });

            return;
        }

        window.location.href = route("login");
    },

    removeFromWishlist(id) {
        this.wishlist.splice(this.wishlist.indexOf(id), 1);

        axios.delete(
            route("account.wishlist.products.destroy", { product: id })
        );
    },

    inCompareList(id) {
        return this.compareList.includes(id);
    },

    syncCompareList(id) {
        if (this.inCompareList(id)) {
            this.removeFromCompareList(id);

            return;
        }

        this.addToCompareList(id);
    },

    addToCompareList(id) {
        this.compareList.push(id);

        axios.post(route("compare.store"), {
            productId: id,
        });
    },

    async removeFromCompareList(id) {
        this.compareList.splice(this.compareList.indexOf(id), 1);

        await axios.delete(route("compare.destroy", { productId: id }));
    },
});
