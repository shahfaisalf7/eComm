@extends('storefront::public.layout')

@section('title', trans('storefront::categories.all_categories'))

@section('content')
    <section class="all-categories-wrap">
        <div class="container">
            <div class="all-categories">
                @forelse ($categories as $category)
                    <div class="single-category">
                        <p class="section-title">
                            <a href="{{ $category->url() }}" title="{{ $category->name }}">
                                {{ $category->name }}
                            </a>
                        </p>

                        @if ($category->items->isNotEmpty())
                            <ul class="list-inline single-category-list">
                                @foreach ($category->items as $subCategory)
                                    <li>
                                        <a href="{{ $subCategory->url() }}" title="{{ $subCategory->name }}">
                                            {{ $subCategory->name }}
                                        </a>

                                        @include('storefront::public.categories.index.sub_category_items')
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @empty
                    @include('storefront::public.categories.index.empty_category')
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('globals')
    @vite([
        'modules/Storefront/Resources/assets/public/sass/pages/categories/main.scss',
    ])
@endpush
