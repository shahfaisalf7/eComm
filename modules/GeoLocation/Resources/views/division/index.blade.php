@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Divisions')

    <li class="active">{{ 'Divisions' }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'geo.division')
    @slot('name', 'Division')

    <div class="box box-primary">
        <div class="box-body index-table" id="divisions-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')
                        <th>{{ __('SL.') }}</th>
                        <th>{{ __('Division Name') }}</th>
                        <th>{{ __('Description') }}</th>
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
        DataTable.setRoutes('#divisions-table .table', {
            table: '{{ 'admin.geo.division.table' }}',
            edit: '{{ 'admin.geo.division.edit' }}',
            destroy: '{{ 'admin.geo.division.destroy' }}',
        });

        new DataTable('#divisions-table .table', {
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
                    data: 'name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description'
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
