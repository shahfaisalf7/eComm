@extends('report::admin.reports.layout')

@section('filters')
    <div class="form-group">
        <label for="keyword">{{ trans('report::admin.filters.keyword') }}</label>
        <input type="text" name="keyword" class="form-control" id="keyword" value="{{ $request->keyword }}">
    </div>
@endsection

@section('report_result')
    <div class="box-header">
        <p>
            {{ trans('report::admin.filters.report_types.search_report') }}
        </p>
    </div>

    <div class="box-body">
        <div class="table-responsive anchor-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('report::admin.table.keyword') }}</th>
                        <th>{{ trans('report::admin.table.results') }}</th>
                        <th>{{ trans('report::admin.table.hits') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report as $data)
                        <tr>
                            <td>{{ $data->term }}</td>
                            <td>{{ $data->results }}</td>
                            <td>{{ $data->hits }}</td>
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
