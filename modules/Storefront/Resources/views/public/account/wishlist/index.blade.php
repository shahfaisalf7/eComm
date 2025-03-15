@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_wishlist'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_wishlist') }}</li>
@endsection

@section('panel')
    <div x-data="Wishlist" class="panel">
        <div class="panel-header">
            <p>{{ trans('storefront::account.pages.my_wishlist') }}</p>
        </div>

        <div x-cloak class="panel-body" :class="{ loading: fetchingWishlist }">
            <template x-if="wishlistIsEmpty">
                <div class="empty-message">
                    <template x-if="!fetchingWishlist">
                        <p>
                            {{ trans('storefront::account.wishlist.empty_wishlist') }}
                        </p>
                    </template>
                </div>
            </template>

            <template x-if="!wishlistIsEmpty">
                <div class="table-responsive">
                    <table class="table table-borderless my-wishlist-table">
                        <thead>
                            <tr>
                                <th>{{ trans('storefront::account.image') }}</th>
                                <th>{{ trans('storefront::account.product_name') }}</th>
                                <th>{{ trans('storefront::account.wishlist.price') }}</th>
                                <th>{{ trans('storefront::account.wishlist.availability') }}</th>
                                <th>{{ trans('storefront::account.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="product in products.data" :key="product.id">
                                <tr x-data="WishlistItem(product)">
                                    <td>
                                        <div class="product-image">
                                            <img
                                                :src="baseImage"
                                                :class="{ 'image-placeholder': !hasBaseImage }"
                                                :alt="productName"
                                            >
                                        </div>
                                    </td>

                                    <td>
                                        <a :href="productUrl" class="product-name" x-text="productName"></a>
                                    </td>

                                    <td>
                                        <span class="product-price" x-html="productPrice"></span>
                                    </td>

                                    <td>
                                        <span
                                            class="badge"
                                            :class="isOutOfStock ? 'badge-danger' : 'badge-success'"
                                            x-text="isOutOfStock ?
                                                '{{ trans('storefront::account.wishlist.out_of_stock') }}' :
                                                '{{ trans('storefront::account.wishlist.in_stock') }}'
                                            "
                                        >
                                        </span>
                                    </td>

                                    <td>
                                        <button class="btn btn-delete" @click="removeItem(product)">
                                            <i class="las la-trash"></i>

                                            {{ trans('storefront::account.wishlist.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <template x-if="products.total > 10">
            <div class="panel-footer">
                @include('storefront::public.partials.pagination')
            </div>
        </template>
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/account/wishlist/main.scss',
        'modules/Storefront/Resources/assets/public/js/pages/account/wishlist/main.js',
    ])
@endpush
