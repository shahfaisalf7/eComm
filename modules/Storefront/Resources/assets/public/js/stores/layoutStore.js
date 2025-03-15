Alpine.store("layout", {
    sidebarMenuOpen: false,
    sidebarCartOpen: false,
    sidebarFilterOpen: false,
    localizationMenuOpen: false,
    overlay: false,

    get isOpenSidebarMenu() {
        return this.sidebarMenuOpen;
    },

    get isOpenSidebarCart() {
        return this.sidebarCartOpen;
    },

    get isOpenSidebarFilter() {
        return this.sidebarFilterOpen;
    },

    get isOpenlocalizationMenu() {
        return this.localizationMenuOpen;
    },

    openSidebarMenu() {
        this.sidebarMenuOpen = true;

        this.showOverlay();
    },

    closeSidebarMenu() {
        this.sidebarMenuOpen = false;

        this.hideOverlay();
    },

    openSidebarCart(event) {
        if (event) {
            event.preventDefault();
        }

        if (route().current("checkout.create")) {
            window.location.href = route("cart.index");

            return;
        }

        this.sidebarCartOpen = true;

        this.showOverlay();

        $(".cart-icon").hide();
    },

    closeSidebarCart() {
        this.sidebarCartOpen = false;

        this.hideOverlay();

        $(".cart-icon").show();
    },

    openSidebarFilter() {
        this.sidebarFilterOpen = true;

        this.showOverlay();
    },

    closeSidebarFilter() {
        this.sidebarFilterOpen = false;

        this.hideOverlay();
    },

    openLocalizationMenu() {
        this.localizationMenuOpen = true;

        this.showOverlay();
    },

    closeLocalizationMenu() {
        this.localizationMenuOpen = false;

        this.hideOverlay();
    },

    showOverlay() {
        this.overlay = true;
    },

    hideOverlay() {
        this.overlay = false;
    },
});
