@php
    use Illuminate\Support\Str;
@endphp

@extends('storefront::public.layout')
@section('title')
    @if (request()->has('query'))
        {{ trans('storefront::products.search_results_for') }}: "{{ request('query') }}"
    @elseif (!empty($category) && !is_null($category->metaData) && $category->metaData->isNotEmpty())
        {{ $category->metaData->first()->meta_title ?? $categoryName }}
    @elseif (!empty($brand) && !is_null($brand->metaData) && $brand->metaData->isNotEmpty())
        {{ $brand->metaData->first()->meta_title ?? $brandName }}
    @else
        {{ $categoryName ?? $brandName ?? trans('storefront::products.title') }}
    @endif
@endsection
{{--@section('title')--}}
{{--    @if (request()->has('query'))--}}
{{--        {{ trans('storefront::products.search_results_for') }}: "{{ request('query') }}"--}}
{{--    @elseif (!empty($category) && $category->metaData->isNotEmpty())--}}
{{--        {{ $category->metaData->first()->meta_title ?? $categoryName }}--}}
{{--    @else--}}
{{--        {{ $categoryName ?? trans('storefront::products.title') }}--}}
{{--    @endif--}}
{{--@endsection--}}

@push('meta')
    @if (!empty($category) && !is_null($category->metaData) && $category->metaData->isNotEmpty())
        <meta name="description" content="{{ $category->metaData->first()->meta_description ?? '' }}">
    @elseif (!empty($brand) && !is_null($brand->metaData) && $brand->metaData->isNotEmpty())
        <meta name="description" content="{{ $brand->metaData->first()->meta_description ?? '' }}">
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
            initialPerPage: {{ request('perPage', 50) }},
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
                    @if (!empty($category) && !empty($categoryBanner))
                        <div class="d-none d-lg-block categories-banner">
                            <img src="{{ $categoryBanner }}" alt="Category banner">
                        </div>
                    @endif

                    @include('storefront::public.products.index.search_result')

                    @if (!empty($category) && !empty($category->description))
                        @php
                            $descriptionWordCount = str_word_count(strip_tags($category->description));
                            $showToggle = $descriptionWordCount > 100;
                        @endphp
                        <div class="category-description" style="margin-top: 50px; margin-bottom: 50px; text-align: justify;">
                            <div class="description-content {{ $showToggle ? 'description-collapsed' : '' }}">
                                {!! $category->description !!}
                            </div>
                            @if ($showToggle)
                                <button class="show-more-btn btn btn-primary" onclick="toggleDescription(this)">Show More</button>
                                <button class="show-less-btn btn btn-primary" style="display: none;" onclick="toggleDescription(this)">Show Less</button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <style>
        .category-description {
            position: relative;
        }

        .description-content {
            transition: max-height 0.8s cubic-bezier(0.25, 0.1, 0.25, 1); /* Smoother transition with cubic-bezier */
            overflow: hidden;
        }

        .description-collapsed {
            max-height: 200px; /* Adjust this value to control the initial visible height */
        }

        .description-expanded {
            max-height: auto; /* Large enough to fit most content; adjust if needed */
        }

    </style>
@endsection

@push('globals')
    <script>
        FleetCart.langs['storefront::products.showing_results'] = '{{ trans("storefront::products.showing_results") }}';

        function toggleDescription(button) {
            const container = button.closest('.category-description');
            const content = container.querySelector('.description-content');
            const showMoreBtn = container.querySelector('.show-more-btn');
            const showLessBtn = container.querySelector('.show-less-btn');

            if (content.classList.contains('description-collapsed')) {
                content.classList.remove('description-collapsed');
                content.classList.add('description-expanded');
                showMoreBtn.style.display = 'none';
                showLessBtn.style.display = 'inline-block';
            } else {
                content.classList.remove('description-expanded');
                content.classList.add('description-collapsed');
                showMoreBtn.style.display = 'inline-block';
                showLessBtn.style.display = 'none';
            }
        }
    </script>

    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/products/index/main.scss',
        'modules/Storefront/Resources/assets/public/js/pages/products/index/main.js',
    ])
@endpush


