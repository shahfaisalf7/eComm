<div class="dashboard-panel sales-analytics">
    <div class="grid-header clearfix">
        <p>{{ trans('admin::dashboard.sales_analytics_title') }}</p>
    </div>

    <div class="canvas">
        <canvas class="chart" width="400" height="250"></canvas>
    </div>
</div>

@push('globals')
    <script>
        FleetCart.langs['admin::dashboard.sales_analytics.orders'] = '{{ trans('admin::dashboard.sales_analytics.orders') }}';
        FleetCart.langs['admin::dashboard.sales_analytics.sales'] = '{{ trans('admin::dashboard.sales_analytics.sales') }}';
    </script>
@endpush
