<!DOCTYPE html>
<html lang="{{ locale() }}">
    <head>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-PJV4G7ND');</script>
        <!-- End Google Tag Manager -->

        <base href="{{ config('app.url') }}">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <link rel="canonical" href="{{ isset($canonical) ? $canonical : url()->current() }}" />
{{--        <title>--}}
{{--            @if(request()->path() === '/')--}}
{{--                {{ setting('store_name') }} @hasSection('title')- @yield('title')--}}
{{--                @endif--}}
{{--            @else--}}
{{--                @hasSection('title') @yield('title')- {{ setting('store_name') }}--}}
{{--                @else--}}
{{--                    {{ setting('store_name') }}--}}
{{--                @endif--}}
{{--            @endif--}}

{{--        </title>--}}
        <title>@if(request()->path() === '/') {{ setting('store_name') }}@hasSection('title') - @yield('title')@endif
            @else @hasSection('title') @yield('title') - @endif{{ setting('store_name') }}
            @endif
        </title>








        @stack('meta')
        @PWA

        <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ font_url(setting('storefront_display_font', 'Poppins')) }}" rel="stylesheet">

        @include('storefront::public.partials.variables')

        @vite([
            'modules/Storefront/Resources/assets/public/sass/app.scss',
            'modules/Storefront/Resources/assets/public/js/app.js',
            'modules/Storefront/Resources/assets/public/js/main.js'
        ])

        @stack('styles')

        {!! setting('custom_header_assets') !!}

        <script>
            window.FleetCart = {
                baseUrl: '{{ config('app.url') }}',
                rtl: {{ is_rtl() ? 'true' : 'false' }},
                storeName: '{{ setting('store_name') }}',
                storeLogo: '{{ $logo }}',
                currency: '{{ currency() }}',
                locale: '{{ locale() }}',
                loggedIn: {{ auth()->check() ? 'true' : 'false' }},
                csrfToken: '{{ csrf_token() }}',
                cart: {!! $cart !!},
                wishlist: {!! $wishlist !!},
                compareList: {!! $compareList !!},
                langs: {
                    'storefront::storefront.something_went_wrong': '{{ trans('storefront::storefront.something_went_wrong') }}',
                    'storefront::layouts.more_results': '{{ trans('storefront::layouts.more_results') }}'
                },
            };
        </script>

        {!! $schemaMarkup->toScript() !!}

        @stack('globals')

        <script type="module">
            Alpine.start();
        </script>

        @routes
    </head>

    <body
        dir="{{ is_rtl() ? 'rtl' : 'ltr' }}"
        class="page-template {{ is_rtl() ? 'rtl' : 'ltr' }}"
        data-theme-color="{{ $themeColor->toHexString() }}"
    >
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PJV4G7ND"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
        <div x-data="App" class="wrapper">
            @include('storefront::public.layouts.top_nav')
            @include('storefront::public.layouts.header')
            {{-- @include('storefront::public.layouts.navigation') --}}
            @include('storefront::public.layouts.breadcrumb')

            @yield('content')

            @include('storefront::public.home.sections.newsletter_subscription')
            @include('storefront::public.layouts.footer')

            <div
                class="overlay"
                :class="{ active: $store.layout.overlay }"
                @click="hideOverlay"
            >
            </div>

            @include('storefront::public.layouts.sidebar_menu')
            @include('storefront::public.layouts.localization')

            @if (!request()->routeIs('checkout.create'))
                @include('storefront::public.layouts.sidebar_cart')
            @endif

            @include('storefront::public.layouts.global_cart')

            @include('storefront::public.layouts.alert')
            @include('storefront::public.layouts.newsletter_popup')
            @include('storefront::public.layouts.cookie_bar')
            {{-- @include('storefront::public.layouts.scroll_to_top') --}}
        </div>

        @stack('pre-scripts')
        @stack('scripts')

        {!! setting('custom_footer_assets') !!}
    </body>
</html>
