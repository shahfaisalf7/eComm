@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_orders'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_orders') }}</li>
@endsection

@section('panel')
    <div class="panel">
        <div class="panel-header">
            <p>{{ trans('storefront::account.pages.my_orders') }}</p>
        </div>

        <div class="panel-body">
            @if ($orders->isEmpty())
                <div class="empty-message">
                    <p>{{ trans('storefront::account.orders.no_orders') }}</p>
                </div>
            @else
                @include('storefront::public.account.partials.orders_table')
            @endif
        </div>

        <div class="panel-footer">
            {!! $orders->links() !!}
        </div>
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/account/orders/index/main.scss',
    ])
@endpush
