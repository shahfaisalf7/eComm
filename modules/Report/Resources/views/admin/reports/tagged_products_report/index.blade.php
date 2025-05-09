@extends('report::admin.reports.layout')

@section('filters')
    <div class="form-group">
        <label for="tag">{{ trans('report::admin.filters.tag') }}</label>

        <input type="text" name="tag" class="form-control" id="tag" value="{{ $request->tag }}">
    </div>
@endsection

@section('report_result')
    <div class="box-header">
        <p>
            {{ trans('report::admin.filters.report_types.tagged_products_report') }}
        </p>
    </div>

    <div class="box-body">
        <div class="table-responsive anchor-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('report::admin.table.tag') }}</th>
                        <th>{{ trans('report::admin.table.products_count') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report as $tag)
                        <tr>
                            <td>
                                {{ $tag->name }}
                            </td>

                            <td>
                                {{ $tag->products_count }}
                            </td>
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
