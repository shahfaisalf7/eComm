@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.dashboard'))

@section('panel')
    @if ($recentOrders->isNotEmpty())
        <div class="panel">
            <div class="panel-header">
                <p>{{ trans('storefront::account.dashboard.recent_orders') }}</p>

                <a href="{{ route('account.orders.index') }}">
                    {{ trans('storefront::account.dashboard.view_all') }}
                </a>
            </div>

            <div class="panel-body">
                @include('storefront::public.account.partials.orders_table', ['orders' => $recentOrders])
            </div>
        </div>
    @endif

    <div class="panel">
        <div class="panel-header">
            <p>{{ trans('storefront::account.dashboard.account_information') }}</p>

            <a href="{{ route('account.profile.edit') }}">
                {{ trans('storefront::account.dashboard.edit') }}
            </a>
        </div>

        <div class="panel-body">
            <ul class="list-inline user-info">
                <li>
                    <i class="las la-user-circle"></i>

                    <span>{{ $account->full_name }}</span>
                </li>

                <li>
                    <i class="las la-envelope"></i>

                    <span>{{ $account->email }}</span>
                </li>
            </ul>
        </div>
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/account/dashboard/main.scss',
    ])
@endpush
