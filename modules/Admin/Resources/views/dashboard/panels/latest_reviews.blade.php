<div class="dashboard-panel">
    <div class="grid-header">
        <p>{{ trans('admin::dashboard.latest_reviews') }}</p>
    </div>

    <div class="clearfix"></div>

    <div class="table-responsive anchor-table latest-reviews">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ trans('admin::dashboard.table.latest_reviews.product') }}</th>
                    <th>{{ trans('admin::dashboard.table.customer') }}</th>
                    <th>{{ trans('admin::dashboard.table.latest_reviews.rating') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestReviews as $latestReview)
                    <tr>
                        <td>
                            <a href="{{ route('admin.reviews.edit', $latestReview) }}">
                                {{ $latestReview->product->name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.reviews.edit', $latestReview) }}">
                                {{ $latestReview->reviewer_name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.reviews.edit', $latestReview) }}">
                                {{ $latestReview->rating }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="empty" colspan="5">{{ trans('admin::dashboard.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
