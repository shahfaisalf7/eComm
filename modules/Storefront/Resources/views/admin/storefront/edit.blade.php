@extends('admin::layout')

@section('title', trans('storefront::storefront.storefront'))

@section('content_header')
    <p>{{ trans('storefront::storefront.storefront') }}</p>

    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard.index') }}">{{ trans('admin::dashboard.dashboard') }}</a></li>
        <li class="active">{{ trans('storefront::storefront.storefront') }}</li>
    </ol>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.storefront.settings.update') }}" class="form-horizontal" id="storefront-settings-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('settings')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/admin/sass/main.scss',
        'modules/Storefront/Resources/assets/admin/js/main.js',
        'modules/Media/Resources/assets/admin/sass/main.scss',
        'modules/Media/Resources/assets/admin/js/main.js'
    ])
@endpush
