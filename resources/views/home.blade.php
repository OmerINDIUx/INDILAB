@extends('layouts.app')

@section('title', 'INDI Lab')
@section('meta_description', 'Some examples for how sticky sections can be animated.')
@section('meta_keywords', 'sticky, css, gsap, animation, sticky section, layout')

@push('css')
    <link id="menu-css" rel="stylesheet" href="{{ asset('css/index.css') }}" />
    <link id="menu-header-css" rel="stylesheet" href="{{ asset('css/components/menu-header1.css') }}" />
@endpush

@section('content')
    <section id="brand-hero" class="brand">
      <div id="svg-wrapper">
        <!-- SVG inicial (cuadrado) -->
        <div id="svg-square" class="svg-containedor"></div>
        <!-- SVG destino (rectángulo, oculto al inicio) -->
        <div id="svg-rect" class="svg-containedor hidden"></div>
      </div>
      <div class="navigation-arrows2">
        <h2 data-i18n="Co-creating the future"></h2>
      </div>
    </section>

    <script>
      gsap.registerPlugin(ScrollTrigger);

      gsap.to(".navigation-arrows2 h2", {
        opacity: 0,
        y: -15, // movimiento mínimo para que se sienta natural
        ease: "none",
        scrollTrigger: {
          trigger: ".navigation-arrows2",
          start: "top bottom",
          end: "bottom top",
          scrub: true,
        },
      });
    </script>

    <!-- ---------------------aquí inicia el HeroMap------------------------------------------- -->

    <section id="map" class="main-content">
      <div class="stay">
        <!-- ✅ ID cambiado para no repetir "brand" -->
        <div id="brand-layout" class="layout">
          <!-- COLUMNA IZQUIERDA -->
          <div id="left-column" class="left-column">
            <div id="content-row">
              <div class="content--intro">
                <p class="text-large_w">
                  <span data-i18n="hero_intro_text"></span> <br />
                  <span data-i18n="hero_intro_text1"></span><br />
                  <span data-i18n="hero_intro_text2"></span>
                </p>
              </div>
            </div>

            <script>
              // SVGs loaded by global.js
            </script>
          </div>

          <div id="maping-contorler" class="right-column">
            <video
              id="trafficVideo"
              class="mapa_video"
              preload="auto"
              muted
              playsinline
            ></video>
            <div class="center-controls">
              <div class="time-slider-box">
                <p class="text-large_M">
                  <mark class="hx-1 hx-2" data-i18n="CDMX Traffic"></mark>
                </p>
                <span id="hourLabel" class="hour">--:--</span>
                <input id="hourSlider" type="range" min="0" max="23" step="1" />
                <div class="video-footer">
                  <p class="map_food" data-i18n="cdmx traffic page food1"></p>
                  <p class="map_food" data-i18n="cdmx traffic page food2"></p>
                  <p class="map_food" data-i18n="cdmx traffic page food3"></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script src="{{ asset('js/demo4/script_map.js') }}"></script>
    </section>

    <section class="text-image-section2">
      <div class="container2">
        <div class="text-column2">
          <h2 class="title2"></h2>
          <p class="text-content2"></p>
        </div>
        <div class="image-column2">
          <div class="content__img-wrapper2"></div>
        </div>
      </div>
    </section>

    <style>
      .title2 span {
        display: block;
      }
    </style>

    <script src="{{ asset('js/demo4/revealTextImages.js') }}"></script>

    <section class="calltoaction-section">
      <div class="secundary-logo">
        <div class="indi-lab-logo" data-svg="{{ asset('svg/Secundary_Brand.svg') }}"></div>
      </div>

      <script>
        // SVGs loaded by global.js
      </script>

      <div>
        <div class="content content--highlight content--outro section">
          <p class="text-large_w" data-i18n="text of the to Action"></p>
        </div>
      </div>

      <div class="navigation-arrows-whatwedo">
        <a href="{{ url('Who_We_Are.html') }}" class="our-projects">
          <img
            src="{{ asset('svg/brand_icon_right.svg') }}"
            alt="Brand Icon Back"
            class="brand-icon-back"
          />
          <h3 data-i18n="menu about"></h3>
        </a>
      </div>
    </section>

    <!-- ---------------------aquí inicia esl código------------------------------------------- -->
    <div class="navigation-arrows">
      <a href="#map">
        <div class="core-belive">
          <img
            src="{{ asset('svg/brand_icon_back.svg') }}"
            alt="Brand Icon Back"
            class="brand-icon-back"
          />
          <h3 data-i18n="menu home"></h3>
        </div>
      </a>

      <a href="{{ url('What_We_Do.html') }}">
        <div class="our-projects">
          <img
            src="{{ asset('svg/brand_icon_right.svg') }}"
            alt="Brand Icon Back"
            class="brand-icon-back"
          />
          <h3 data-i18n="Our work"></h3>
        </div>
      </a>
    </div>

    <div class="scroll-animation">
      <img src="{{ asset('img/Scroll Down.gif') }}" alt="scroll down" />
    </div>

    @include('partials.newsletter')

@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
    <!-- ------js funcionales sin llamdos html---------- -->
    <script class="branding" src="{{ asset('js/demo4/animation_brand.js') }}"></script>
    <script src="{{ asset('js/demo4/miniscritps.js') }}"></script>
    <script src="{{ asset('js/demo4/RaditionAndWheather.js') }}"></script>
    <script src="{{ asset('js/demo4/butoms_prueba.js') }}"></script>
@endpush
