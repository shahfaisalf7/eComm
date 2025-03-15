@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_downloads'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_downloads') }}</li>
@endsection

@section('panel')
    <div class="panel">
        <div class="panel-header">
            <p>{{ trans('storefront::account.pages.my_downloads') }}</p>
        </div>

        <div class="panel-body">
            @if ($downloads->isEmpty())
                <div class="empty-message">
                    <p>{{ trans('storefront::account.downloads.no_downloadable_files') }}</p>
                </div>
            @else
                @include('storefront::public.account.downloads.partials.downloads_table')
            @endif
        </div>
    </div>
@endsection
