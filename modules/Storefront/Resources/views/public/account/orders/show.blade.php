{{-- @extends('storefront::public.layout') --}}
@extends('storefront::public.account.layout')



@section('title', trans('storefront::account.view_order.view_order'))

@section('breadcrumb')
    <li><a href="{{ route('account.dashboard.index') }}">{{ trans('storefront::account.pages.my_account') }}</a></li>
    <li><a href="{{ route('account.orders.index') }}">{{ trans('storefront::account.pages.my_orders') }}</a></li>
    <li class="active">{{ trans('storefront::account.orders.view_order') }}</li>
@endsection

@section('panel')
    <section class="order-details-wrap">
        <div class="container">
            <div class="order-details-top">
                <p class="section-title">{{ trans('storefront::account.view_order.view_order') }}</p>

                <div class="row">
                    @include('storefront::public.account.orders.show.order_information')
                    @include('storefront::public.account.orders.show.billing_address')
                    @include('storefront::public.account.orders.show.shipping_address')
                </div>
            </div>

            @include('storefront::public.account.orders.show.items_ordered')
            @include('storefront::public.account.orders.show.order_totals')
        </div>
    </section>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/account/orders/show/main.scss',
    ])
@endpush
