<div class="address-information-wrapper">
    <p class="section-title">{{ trans('order::orders.address_information') }}</p>

    <div class="row">
        <div class="col-md-6">
            <div class="billing-address">
                <p class="pull-left">{{ trans('order::orders.billing_address') }}</p>

                <span>
                    {{ $order->billing_full_name }}
                    <br>
                    {{ $order->billing_address_1 }}
                    <br>

                    @if ($order->billing_address_2)
                        {{ $order->billing_address_2 }}
                        <br>
                    @endif

                    {{ $order->billing_state }}, {!! $order->billing_city !!}, {{ $order->billing_zone }}
                    <br>
                    {{ $order->billing_country_name }}
                </span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="shipping-address">
                <p class="pull-left">{{ trans('order::orders.shipping_address') }}</p>

                <span>
                    {{ $order->shipping_full_name }}
                    <br>
                    {{ $order->shipping_address_1 }}
                    <br>

                    @if ($order->shipping_address_2)
                        {{ $order->shipping_address_2 }}
                        <br>
                    @endif

                    {{ $order->shipping_state }}, {!! $order->shipping_city !!}, {{ $order->shipping_zone }}
                    <br>
                    {{ $order->shipping_country_name }}
                </span>
            </div>
        </div>
    </div>
</div>
