Alpine.data("CookieBar", () => ({
    show: false,

    init() {
        setTimeout(() => {
            this.show = true;
        }, 1000);
    },

    decline() {
        this.show = false;

        axios.delete(route("storefront.cookie_bar.destroy"));
    },

    accept() {
        this.show = false;

        axios.delete(route("storefront.cookie_bar.destroy"));
    },
}));
