<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PJV4G7ND');</script>
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LL6N3E7SEB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-LL6N3E7SEB');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
        integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Icon: font-awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    {{-- Custom Css --}}
    <link rel="stylesheet" href="{{ asset('modules/Floramom/css/custom/media.css') }}">

    <title>{{ config('app.name') }}</title>
    @stack('storefrontCss')
    @stack('css')
</head>

<body class="container mx-auto">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PJV4G7ND"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    @include('floramom::layouts.header.header')
    @include('floramom::layouts.breadcrumb.breadcrumb')
    @yield('content')
    @include('floramom::layouts.footer.footer')

    {{-- Custom Js --}}
    <script src="{{ asset('modules/Floramom/js/custom/header/header.js') }}"></script>

    <!-- jQuery (Full Version) CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- Axios Js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.5.1/dist/axios.min.js"></script>

    @stack('js')
</body>

</html>
