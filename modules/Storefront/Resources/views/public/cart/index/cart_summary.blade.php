<template x-if="!cartIsEmpty">
    <aside class="order-summary-wrap">
        <div class="order-summary">
            <div class="order-summary-top">
                <p class="section-title">{{ trans('storefront::cart.cart_summary') }}</p>
            </div>

            <div class="order-summary-middle">
                <ul class="list-inline order-summary-list">
                    <li>
                        <label>{{ trans('storefront::cart.total') }}</label>

                        <span x-text="formatCurrency($store.state.cartSubTotal)"></span>
                    </li>
                </ul>
            </div>

            <div class="order-summary-bottom">
                @if (auth()->check())
                    <a href="{{ route('checkout.create') }}" class="btn btn-primary btn-proceed-to-checkout">
                        {{ trans('storefront::cart.proceed_to_checkout') }}
                    </a>
                @else
                    <a data-bs-toggle="modal" data-bs-target="#static_login_modal"
                        class="btn btn-primary btn-proceed-to-checkout">
                        {{ trans('storefront::cart.proceed_to_checkout') }}
                    </a>
                @endif
            </div>
        </div>
    </aside>
</template>
