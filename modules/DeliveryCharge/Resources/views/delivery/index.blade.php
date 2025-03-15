@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Delivery Charge')

    <li class="active">{{ __('Delivery Charge') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'delivery.charge')
    @slot('name', 'Delivery Charege')

    <div class="box box-primary">
        <div class="box-body index-table" id="delivery-charge-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')
                        <th>{{ __('SL.') }}</th>
                        <th>{{ __('City Name') }}</th>
                        <th>{{ __('Division Name') }}</th>
                        <th>{{ __('Charge') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th data-sort>{{ trans('admin::admin.table.created') }}</th>
                    </tr>
                @endslot
            @endcomponent
        </div>
    </div>
@endcomponent

@push('scripts')
    <script type="module">
        DataTable.setRoutes('#delivery-charge-table .table', {
            table: '{{ 'admin.delivery.charge.table' }}',
            edit: '{{ 'admin.delivery.charge.edit' }}',
            destroy: '{{ 'admin.delivery.charge.destroy' }}',
        });

        new DataTable('#delivery-charge-table .table', {
            columns: [{
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '3%'
                },
                {
                    data: 'sl',
                    width: '5%'
                },
                {
                    data: 'city_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'division_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'charge'
                },
                {
                    data: 'status'
                },
                {
                    data: 'created_at',
                }
            ],
        });
    </script>
@endpush
