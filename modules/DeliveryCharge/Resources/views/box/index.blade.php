@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Box Charge')

    <li class="active">{{ __('Box Charge') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'box.charge')
    @slot('name', 'Box Charege')

    <div class="box box-primary">
        <div class="box-body index-table" id="box-charge-table">
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
        DataTable.setRoutes('#box-charge-table .table', {
            table: '{{ 'admin.box.charge.table' }}',
            edit: '{{ 'admin.box.charge.edit' }}',
            destroy: '{{ 'admin.box.charge.destroy' }}',
        });
        debugger;

        new DataTable('#box-charge-table .table', {
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
