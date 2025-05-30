@extends('report::admin.reports.layout')

@section('filters')
    @include('report::admin.reports.filters.from')
    @include('report::admin.reports.filters.to')
    @include('report::admin.reports.filters.status')
    @include('report::admin.reports.filters.group')

    <div class="form-group">
        <label for="shipping-method">{{ trans('report::admin.filters.shipping_method') }}</label>

        <select name="shipping_method" id="shipping-method" class="custom-select-black">
            <option value="">{{ trans('report::admin.filters.please_select') }}</option>

            @foreach ($shippingMethods as $name => $shippingMethod)
                <option value="{{ $name }}" {{ $request->shipping_method === $name ? 'selected' : '' }}>
                    {{ $shippingMethod->label }}
                </option>
            @endforeach
        </select>
    </div>
@endsection

@section('report_result')
    <div class="box-header">
        <p>
            {{ trans('report::admin.filters.report_types.shipping_report') }}
        </p>
    </div>

    <div class="box-body">
        <div class="table-responsive anchor-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('report::admin.table.date') }}</th>
                        <th>{{ trans('report::admin.table.shipping_method') }}</th>
                        <th>{{ trans('report::admin.table.orders') }}</th>
                        <th>{{ trans('report::admin.table.total') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report as $data)
                        <tr>
                            <td>{{ $data->start_date->toFormattedDateString() }} - {{ $data->end_date->toFormattedDateString() }}</td>
                            <td>{{ $data->shipping_method }}</td>
                            <td>{{ $data->total_orders }}</td>
                            <td>{{ $data->total->format() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="8">{{ trans('report::admin.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pull-right">
                {!! $report->links() !!}
            </div>
        </div>
    </div>
@endsection
