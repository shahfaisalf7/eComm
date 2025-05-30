<!DOCTYPE html>
<html lang="{{ locale() }}">

<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PJV4G7ND');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LL6N3E7SEB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-LL6N3E7SEB');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ trans('order::print.invoice') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['modules/Order/Resources/assets/admin/sass/print.scss'])
</head>

<body class="{{ is_rtl() ? 'rtl' : 'ltr' }}">
    <!--[if lt IE 8]>
        <p>You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a>
            to improve your experience.</p>
        <![endif]-->

    <div class="container">
        <div class="invoice-wrapper clearfix">
            <div class="row">
                <div class="invoice-header clearfix">
                    <div class="col-md-3">
                        <div class="store-name">
                            <h1>{{ setting('store_name') }}</h1>
                        </div>
                    </div>

                    <div class="col-md-9 clearfix">
                        <div class="invoice-header-right pull-right">
                            <span class="title">{{ trans('order::print.invoice') }}</span>

                            <div class="invoice-info clearfix">
                                <div class="invoice-id">
                                    <label for="invoice-id">{{ trans('order::print.invoice_id') }}:</label>
                                    <span>#{{ $order->id }}</span>
                                </div>

                                <div class="invoice-date">
                                    <label for="invoice-date">{{ trans('order::print.date') }}:</label>
                                    <span>{{ $order->created_at->format('Y / m / d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="invoice-body clearfix">
                <div class="invoice-details-wrapper">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="invoice-details">
                                <p>{{ trans('order::print.order_details') }}</p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>{{ trans('order::print.email') }}:</td>
                                                <td>{{ $order->customer_email }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ trans('order::print.phone') }}:</td>
                                                <td>{{ $order->customer_phone }}</td>
                                            </tr>

                                            {{-- @if ($order->shipping_method)
                                                <tr>
                                                    <td>{{ trans('order::print.shipping_method') }}:</td>
                                                    <td>{{ $order->shipping_method }}</td>
                                                </tr>
                                            @endif --}}

                                            <tr>
                                                <td>{{ trans('order::print.payment_method') }}:</td>
                                                <td>
                                                    {{ $order->payment_method }}

                                                    @if ($order->payment_method === 'Bank Transfer')
                                                        <br>

                                                        <span style="color: #999; font-size: 13px;">{!! setting('bank_transfer_instructions') !!}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="invoice-address">
                                <p>{{ trans('order::print.billing_address') }}</p>

                                <span>{{ $order->billing_full_name }}</span>
                                <span>{{ $order->billing_address_1 }}</span>
                                <span>{{ $order->billing_address_2 }}</span>
                                <span>{{ $order->billing_state }}, {!! $order->billing_city !!}, {{ $order->billing_zone }}</span>
                                <span>{{ $order->billing_country_name }}</span>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <div class="invoice-address">
                                <p>{{ trans('order::print.shipping_address') }}</p>

                                <span>{{ $order->shipping_full_name }}</span>
                                <span>{{ $order->shipping_address_1 }}</span>
                                <span>{{ $order->shipping_address_2 }}</span>
                                <span>{{ $order->shipping_state }}, {!! $order->shipping_city !!}, {{ $order->shipping_zone }}</span>
                                <span>{{ $order->shipping_country_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="cart-list">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('order::print.product') }}</th>
                                        <th>{{ trans('order::print.unit_price') }}</th>
                                        <th>{{ trans('order::print.quantity') }}</th>
                                        <th>{{ trans('order::print.line_total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->products as $product)
                                        <tr>
                                            <td>
                                                <span>{{ $product->name }}</span>

                                                @if ($product->hasAnyVariation())
                                                    <div class="option">
                                                        @foreach ($product->variations as $variation)
                                                            <span>
                                                                {{ $variation->name }}:

                                                                <span>
                                                                    {{ $variation->values()->first()?->label }}{{ $loop->last ? '' : ',' }}
                                                                </span>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if ($product->hasAnyOption())
                                                    <div class="option">
                                                        @foreach ($product->options as $option)
                                                            <span>
                                                                {{ $option->name }}:

                                                                <span>
                                                                    @if ($option->option->isFieldType())
                                                                        {{ $option->value }}
                                                                    @else
                                                                        {{ $option->values->implode('label', ', ') }}
                                                                    @endif
                                                                </span>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <label
                                                    class="visible-xs">{{ trans('order::print.unit_price') }}:</label>
                                                <span>{{ $product->unit_price->convert($order->currency, $order->currency_rate)->convert($order->currency, $order->currency_rate)->format($order->currency) }}</span>
                                            </td>

                                            <td>
                                                <label class="visible-xs">{{ trans('order::print.quantity') }}:</label>
                                                <span>{{ $product->qty }}</span>
                                            </td>
                                            <td>
                                                <label
                                                    class="visible-xs">{{ trans('order::print.line_total') }}:</label>
                                                <span>{{ $product->line_total->convert($order->currency, $order->currency_rate)->format($order->currency) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="total pull-right">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ trans('order::print.subtotal') }}</td>
                                    <td>{{ $order->sub_total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                                    </td>
                                </tr>

                                {{-- @if ($order->hasShippingMethod()) --}}
                                    <tr>
                                        <td>Delivery Charge</td>
                                        <td>{{ $order->shipping_cost->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                                        </td>
                                    </tr>
                                {{-- @endif --}}

                                @if ($order->hasCoupon())
                                    <tr>
                                        <td>
                                            {{ trans('order::orders.coupon') }} <span
                                                class="coupon-code">({{ $order->coupon->code }})</span>
                                        </td>
                                        <td>
                                            &#8211;{{ $order->discount->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($order->taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td class="text-right">
                                            {{ $tax->order_tax->amount->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                                        </td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td>{{ trans('order::print.total') }}</td>
                                    <td>{{ $order->total->convert($order->currency, $order->currency_rate)->format($order->currency) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        window.print();
    </script>
</body>

</html>
