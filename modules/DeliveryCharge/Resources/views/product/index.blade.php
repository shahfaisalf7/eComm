@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Product Charge')

    <li class="active">{{ __('Product Charge') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'product.charge')
    @slot('name', 'Product Charege')

    <div class="box box-primary">
        <div class="box-body index-table" id="product-charge-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')
                        <th>{{ __('SL.') }}</th>
                        <th>{{ __('Weight') }}</th>
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
        DataTable.setRoutes('#product-charge-table .table', {
            table: '{{ 'admin.product.charge.table' }}',
            edit: '{{ 'admin.product.charge.edit' }}',
            destroy: '{{ 'admin.product.charge.destroy' }}',
        });
        debugger;

        new DataTable('#product-charge-table .table', {
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
                    data: 'weight',
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
