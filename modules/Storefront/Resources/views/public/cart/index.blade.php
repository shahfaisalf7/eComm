@extends('storefront::public.layout')

@section('title', trans('storefront::cart.cart'))

@section('content')
    <div x-data="Cart">
        <section class="cart-wrap">
            <div class="container">
                <template x-if="!cartIsEmpty">
                    <div>
                        @include('storefront::public.cart.index.steps')

                        <div class="cart">
                            <div class="cart-inner">
                                @include('storefront::public.cart.index.cart_table')
                            </div>

                            @include('storefront::public.cart.index.cart_summary')
                        </div>
                    </div>
                </template>

                @include('storefront::public.cart.index.empty_cart')
            </div>
        </section>
        
        @if ($crossSellProducts->isNotEmpty())
            @include('storefront::public.partials.landscape_products', [
                'title' => trans('storefront::product.you_might_also_like'),
                'routeName' => 'cart.cross_sell_products.index',
                'watchState' => '$store.state.cartIsEmpty'
            ])
        @endif
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/cart/main.scss',  
        'modules/Storefront/Resources/assets/public/js/pages/cart/main.js',
    ])
@endpush
