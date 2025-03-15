@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Cities')

    <li class="active">{{ 'Cities' }}</li>
@endcomponent


@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'geo.cities')
    @slot('name', 'City')

    <div class="box box-primary">
        <div class="box-body index-table" id="cities-table">
            @component('admin::components.table')
                @slot('thead')
                    <tr>
                        @include('admin::partials.table.select_all')
                        <th>{{ __('SL.') }}</th>
                        <th>{{ __('City Name') }}</th>
                        <th>{{ __('Division Name') }}</th>
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
        DataTable.setRoutes('#cities-table .table', {
            table: '{{ 'admin.geo.cities.table' }}',
            edit: '{{ 'admin.geo.cities.edit' }}',
            destroy: '{{ 'admin.geo.cities.destroy' }}',
        });

        new DataTable('#cities-table .table', {
            columns: [{
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '3%'
                }, {
                    data: 'sl',
                    width: '5%'
                },
                {
                    data: 'name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'division_name',
                    orderable: false,
                    searchable: false
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
