@extends('storefront::public.layout')

@section('title', trans('storefront::compare.compare'))

@section('content')
    <div x-data="Compare({{ $compare }})">
        <section class="compare-wrap">
            <div class="container">
                @include('storefront::public.compare.partials.compare_table')
                @include('storefront::public.compare.partials.empty_compare_table')
            </div>
        </section>

        @if ($compare->relatedProducts()->isNotEmpty())
            @include('storefront::public.partials.landscape_products', [
                'title' => trans('storefront::product.related_products'),
                'routeName' => 'compare.related_products.index',
                'watchState' => '$store.state.compareIsEmpty'
            ])
        @endif
    </div>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/compare/main.scss',
        'modules/Storefront/Resources/assets/public/js/pages/compare/main.js',
    ])
@endpush
