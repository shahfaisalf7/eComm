export default function (product) {
    return {
        product: product,
        item: product.variant || product,
        addingToCart: false,

        get productName() {
            return this.product.name;
        },

        get productUrl() {
            return route("products.show", {
                slug: this.product.slug,
                ...(this.hasAnyVariant && {
                    variant: this.item.uid,
                }),
            });
        },

        get productPrice() {
            return this.item.formatted_price;
        },

        get regularPrice() {
            return this.item.price.inCurrentCurrency.amount;
        },

        get hasSpecialPrice() {
            return this.item.special_price !== null;
        },

        get hasPercentageSpecialPrice() {
            return this.item.has_percentage_special_price;
        },

        get specialPrice() {
            return this.item.selling_price.inCurrentCurrency.amount;
        },

        get specialPricePercent() {
            return Math.round(
                ((this.regularPrice - this.specialPrice) / this.regularPrice) *
                    100
            );
        },

        get hasAnyVariant() {
            return this.product.variant !== null;
        },

        get hasAnyOption() {
            return this.product.options_count > 0;
        },

        get hasNoOption() {
            return !this.hasAnyOption;
        },

        get hasAnyMedia() {
            return this.item.media.length !== 0;
        },

        get hasBaseImage() {
            if (this.hasAnyVariant) {
                return this.item.base_image.length !== 0 ||
                    this.product.base_image.length !== 0
                    ? true
                    : false;
            }

            return this.item.base_image.length !== 0;
        },

        get baseImage() {
            return this.hasBaseImage
                ? this.item.base_image.path || this.product.base_image.path
                : `${window.FleetCart.baseUrl}/build/assets/image-placeholder.png`;
        },

        get isInStock() {
            return this.item.is_in_stock;
        },

        get orderProductCount() {
            return this.item?.order_products_count ?? 0;
        },

        get isOutOfStock() {
            return this.item.is_out_of_stock;
        },

        get doesManageStock() {
            return this.item.does_manage_stock;
        },

        get isNew() {
            return !this.isOutOfStock && this.product.is_new;
        },

        syncWishlist() {
            this.$store.state.syncWishlist(this.product.id);
        },

        syncCompareList() {
            this.$store.state.syncCompareList(this.product.id);
        },

        addToCart() {
            if (this.addingToCart) {
                return;
            }

            this.addingToCart = true;

            axios
                .post(
                    route("cart.items.store", {
                        product_id: this.product.id,
                        qty: 1,
                        ...(this.hasAnyVariant && {
                            variant_id: this.item.id,
                        }),
                    })
                )
                .then((response) => {
                    this.$store.state.updateCart(response.data);
                    // this.$store.layout.openSidebarCart();
                })
                .catch((error) => {
                    notify(error.response.data.message);
                })
                .finally(() => {
                    this.addingToCart = false;
                });
        },
    };
}
