<div class="order-details-bottom">
    <ul class="list-inline order-summary-list">
        <li>
            <label>{{ trans('storefront::account.view_order.subtotal') }}</label>

            <span>
                {{ $order->sub_total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
            </span>
        </li>

            <li>
                <label>Delivery Charge</label>

                <span>
                    {{ $order->shipping_cost->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                </span>
            </li>

        @foreach ($order->taxes as $tax)
            <li>
                <label>{{ $tax->name }}</label>

                <span>
                    {{ $tax->order_tax->amount->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                </span>
            </li>
        @endforeach

        @if ($order->hasCoupon())
            <li>
                <label>
                    {{ trans('storefront::account.view_order.coupon') }}
                    <span class="coupon-code">({{ $order->coupon->code }})</span>
                </label>

                <span>
                    -{{ $order->discount->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                </span>
            </li>
        @endif
    </ul>

    <div class="order-summary-total">
        <label>{{ trans('storefront::account.view_order.total') }}</label>

        <span class="total-price">
            {{ $order->total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
        </span>
    </div>
</div>
