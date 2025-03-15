<template x-if="!cartIsEmpty">
    <aside class="order-summary-wrap">
        <div class="order-summary">
            <div class="order-summary-top">
                <p class="section-title">{{ trans('storefront::cart.order_summary') }}</p>
            </div>

            <div class="order-summary-middle" :class="{ loading: loadingOrderSummary }">
                <ul class="list-inline order-summary-list">
                    <li>
                        <label>{{ trans('storefront::cart.subtotal') }}</label>

                        <span x-text="$store.state.cartSubTotal"></span>
                    </li>
                </ul>

                <div class="order-summary-total">
                    <label>{{ trans('storefront::cart.total') }}</label>

                    <span class="total-price" x-text="$store.state.cartTotal"></span>
                </div>
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
