<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="@yield('html-class')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'INDI Lab')</title>
    
    <!-- Meta tags -->
    <meta name="description" content="@yield('meta_description', 'INDI Lab Project')" />
    <meta name="keywords" content="@yield('meta_keywords', 'indi, lab')" />
    <meta name="author" content="Codrops" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- GSAP (Local) -->
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <script src="{{ asset('js/ScrollTrigger.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/TextPlugin.min.js"></script>

    <!-- Global Libs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fontfaceobserver/2.1.0/fontfaceobserver.standalone.js"></script>
    <script src="{{ asset('js/lenis.min.js') }}"></script>
    <script src="{{ asset('js/splitting.min.js') }}"></script>

    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/layout/secundary-brand.css') }}" />
    
    <!-- Page Specific CSS -->
    @stack('css')

    <script>
      window.rootPath = {!! json_encode(asset('/')) !!};
      document.documentElement.className = "js";
    </script>
</head>
<body class="@yield('body-class')">
    @if(!request()->is('admin*') && !request()->is('projects*'))
    <div class="top-bar"></div>
    @endif

    @if(!request()->is('admin*') && !request()->is('projects*') && !request()->routeIs('home'))
    <!-- Persistent Logo -->
    <a href="{{ route('home') }}" class="secundary-brand-link">
        <div class="secundary-brand" data-svg="{{ asset('svg/indi-lab_Vertical_Animate.svg') }}"></div>
    </a>
    @endif

    @yield('content')

    @if(!request()->is('admin*') && !request()->is('projects*'))
    @include('partials.menu')
    <script src="{{ asset('js/demo4/menu.js') }}"></script>
    @endif

    <script src="{{ asset('js/global.js') }}"></script>
    @include('components.media-manager')
    
    {{-- Analytics Tracking --}}
    <script>
        window.analyticsConfig = {
            endpoint: '{{ route("analytics.track") }}',
            sessionId: '{{ session()->getId() }}',
            userId: {{ auth()->id() ?? 'null' }}
        };
    </script>
    <script src="{{ asset('js/analytics-tracker.js') }}" defer></script>
    
    @stack('scripts')
</body>
</html>
