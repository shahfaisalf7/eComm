<div class="col-lg-6 col-sm-9">
    <div class="order-shipping-details">
        <p>{{ trans('storefront::account.view_order.shipping_address') }}</p>

        <address>
            <span>{{ $order->shipping_full_name }}</span>
            <span>{{ $order->shipping_address_1 }}</span>

            @if ($order->shipping_address_2)
                <span>{{ $order->shipping_address_2 }}</span>
            @endif

            <span>{{ $order->shipping_state }}, {!! $order->shipping_city !!}, {{ $order->shipping_zone }}</span>
            <span>{{ $order->shipping_country_name }}</span>
        </address>
    </div>
</div>
