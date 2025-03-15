@extends('storefront::public.layout')

@section('title')
    @if (request()->has('query'))
        {{ trans('storefront::products.search_results_for') }}: "{{ request('query') }}"
    @elseif (request()->has('category') && $category->metaData->isNotEmpty())
        {{ $category->metaData->first()->meta_title ?? $categoryName }}
    @else
        {{ $categoryName ?? trans('storefront::products.shop') }}
    @endif
@endsection

@push('meta')
    @if (request()->has('category') && $category->metaData->isNotEmpty())
        <meta name="description" content="{{ $category->metaData->first()->meta_description ?? '' }}">
    @endif
@endpush

@section('content')
    <section
        x-data="ProductIndex({
            initialQuery: '{{ addslashes(request('query')) }}',
            initialBrandName: '{{ addslashes($brandName ?? '') }}',
            initialBrandBanner: '{{ $brandBanner ?? '' }}',
            initialBrandSlug: '{{ request('brand') }}',
            initialCategoryName: '{{ addslashes($categoryName ?? '') }}',
            initialCategoryBanner: '{{ $categoryBanner ?? '' }}',
            initialCategorySlug: '{{ request('category') }}',
            initialTagName: '{{ addslashes($tagName ?? '') }}',
            initialTagSlug: '{{ request('tag') }}',
            initialAttribute: {{ json_encode(request('attribute', [])) }},
            minPrice: {{ $minPrice ?? 0 }},
            maxPrice: {{ $maxPrice ?? 1000 }},
            initialSort: '{{ request('sort', 'latest') }}',
            initialPage: {{ request('page', 1) }},
            initialPerPage: {{ request('perPage', 20) }},
            initialViewMode: '{{ request('viewMode', 'grid') }}'
        })"
        class="product-search-wrap"
    >
        <div class="container">
            <div class="product-search">
                <div class="product-search-left">
                    @if ($categories->isNotEmpty())
                        <div class="d-none d-lg-block browse-categories-wrap">
                            <p class="section-title">
                                {{ trans('storefront::products.browse_categories') }}
                            </p>
                            @include('storefront::public.products.index.browse_categories')
                        </div>
                    @endif

                    @include('storefront::public.products.index.filter')
                    @include('storefront::public.products.index.latest_products')
                </div>

                <div class="product-search-right">
                    @if ($categoryBanner)
                        <div class="d-none d-lg-block categories-banner">
                            <img src="{{ $categoryBanner }}" alt="Category banner">
                        </div>
                    @endif

                    @include('storefront::public.products.index.search_result')
                </div>
            </div>
        </div>
    </section>
@endsection

@push('globals')
    <script>
        FleetCart.langs['storefront::products.showing_results'] = '{{ trans("storefront::products.showing_results") }}';
    </script>

    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/products/index/main.scss',
        'modules/Storefront/Resources/assets/public/js/pages/products/index/main.js',
    ])
@endpush
