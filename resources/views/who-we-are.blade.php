@extends('layouts.app')

@section('title', 'Who We Are | INDI Lab')

@push('css')
    <link id="menu-css" rel="stylesheet" href="{{ asset('css/who-we-are.css') }}" />
    <link id="menu-header-css" rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}">
@endpush

@section('content')
  <!-- Contenido principal -->
  <section class="hero">
    <video class="hero-video" autoplay muted loop playsinline aria-hidden="true">
      <source src="{{ asset('video/videohoweare.mov') }}" type="video/mp4" />
      Tu navegador no soporta el elemento de video.
    </video>
    <div class="hero-text">
      <h1 data-i18n="who we are"> </h1>
      <h2 data-i18n="who we are sub"></h2>
      <h2 data-i18n="who we are sub1"></h2>
    </div>
  </section>

  <section class="intro">
    <p>
      <span data-i18n="who mwe are intro"></span>
      <span class="pdp" data-i18n="who mwe are spam"></span>
      <span data-i18n="who we are text"></span>
    </p>

    <div class="secundary-logo">
      <div class="indi-lab-logo" data-svg="{{ asset('svg/Secundary_Brand.svg') }}"></div>
    </div>

    <script>
      // SVGs loaded by global.js
    </script>
  </section>

  <section class="statement">
    <div id="content-row">
      <div class="content-text">
        <h2 data-i18n="We design prototype"></h2>
        <div class="corner corner-bottom-left" data-svg="{{ asset('svg/cornner_bottom_left.svg') }}"></div>
        <div class="corner corner-top-right" data-svg="{{ asset('svg/cornner_top_right.svg') }}"></div>
      </div>
    </div>
  </section>

  <script>
    // Corner animation moved to js/pages/who_we_are.js
  </script>

  <section class="list">
    <div class="phrase">
      <span class="pdp" data-i18n="Scalable solutions"></span> <span data-i18n="Scalable solutions text"></span>
    </div>
    <div class="phrase">

      <span class="pdp" data-i18n="Civic tech and gov tech ventures"></span> <span
        data-i18n="Civic tech and gov tech ventures text"></span>


    </div>
    <div class="phrase">

      <span class="pdp" data-i18n="Applied research, data analysis, and strategic studies"></span> <span
        data-i18n="Applied research, data analysis, and strategic studies text"></span>
    </div>
  </section>

  <script>
   // List animation moved to js/pages/who_we_are.js
  </script>

  <section class="closing">

    <p class="text-large_w" data-i18n="by into Grupo INDI’s"></p>
    
    <div class="navigation-arrows-whatwedo">

      <a href="{{ url('our_aproach.html') }}" class="our-projects">
        <img src="{{ asset('svg/brand_icon_right.svg') }}" alt="Brand Icon Back" class="brand-icon-back" />
        <h3 data-i18n="menu contact"></h3>
      </a>
    </div>
  </section>

  <script>
    // Hero animation moved to js/pages/who_we_are.js
  </script>

  <script>
  // Color randomization moved to js/pages/who_we_are.js
  </script>

  <!-- Logo -->
  <a href="{{ route('home') }}">
    <div class="secundary-brand" data-svg="{{ asset('svg/indi-lab_Vertical_Animate.svg') }}"></div>
  </a>

  @include('partials.newsletter')
@endsection

@push('scripts')
  <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
  <script src="{{ asset('js/pages/who_we_are.js') }}"></script>
@endpush
