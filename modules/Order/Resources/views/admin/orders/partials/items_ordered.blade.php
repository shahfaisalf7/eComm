<div class="items-ordered-wrapper">
    <p class="section-title">{{ trans('order::orders.items_ordered') }}</p>

    <div class="row">
        <div class="col-md-12">
            <div class="items-ordered">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('order::orders.product') }}</th>
                                <th>{{ trans('order::orders.unit_price') }}</th>
                                <th>{{ trans('order::orders.quantity') }}</th>
                                <th>{{ trans('order::orders.line_total') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>
                                        @if ($product->trashed())
                                            {{ $product->name }}
                                        @else
                                            <a href="{{ route('admin.products.edit', $product->product->id) }}">{{ $product->name }}</a>
                                        @endif

                                        @if ($product->hasAnyVariation())
                                            <br>
                                            @foreach ($product->variations as $variation)
                                                <span>
                                                    {{ $variation->name }}:

                                                    <span>
                                                        {{ $variation->values()->first()?->label }}{{ $loop->last ? "" : "," }}
                                                    </span>
                                                </span>
                                            @endforeach
                                        @endif

                                        @if ($product->hasAnyOption())
                                            <br>
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
                                        @endif
                                    </td>

                                    <td>
                                        {{ $product->unit_price->format() }}
                                    </td>

                                    <td>{{ $product->qty }}</td>

                                    <td>
                                        {{ $product->line_total->format() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
