<div class="col-lg-6 col-sm-9">
    <div class="order-billing-details">
        <p>{{ trans('storefront::account.view_order.billing_address') }}</p>

        <address>
            <span>{{ $order->billing_full_name }}</span>
            <span>{{ $order->billing_address_1 }}</span>

            @if ($order->billing_address_2)
                <span>{{ $order->billing_address_2 }}</span>
            @endif

            <span>{{ $order->billing_state }}, {!! $order->billing_city !!}, {{ $order->billing_zone }}</span>
            <span>{{ $order->billing_country_name }}</span>
        </address>
    </div>
</div>
