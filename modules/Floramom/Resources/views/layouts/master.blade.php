<!DOCTYPE html>
<html lang="en">

<head>
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
