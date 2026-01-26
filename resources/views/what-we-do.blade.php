@extends('layouts.app')

@section('title', 'What We Do | INDI Lab')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}" />
@endpush

@section('content')
    <!-- Hero -->
    <section class="hero-carousel-section">
      <div class="carousel-background">
        <div class="overlay"></div>
        <img
          src="{{ asset('img/What we do/pexels-gabo-orozco-lucio-233483298-28764098.jpg') }}"
          class="carousel-img active"
        />
        <img
          src="{{ asset('img/What we do/pexels-susan-flores-232226967-12294911.jpg') }}"
          class="carousel-img"
        />
        <img
          src="{{ asset('img/What we do/pexels-victor-armas-262050668-12930992.jpg') }}"
          class="carousel-img"
        />
      </div>

      <div class="carousel-content">
        <h3 id="carousel-phrase" data-i18n="carrusel indi lab"></h3>

        <!-- 🔥 Línea de búsqueda con sugerencias -->
        <div class="search-line">
          <label for="carousel-search" data-i18n="INDI Lab #"></label>
          <div class="input-wrapper">
            <input id="carousel-search" type="text" autocomplete="off" />
            <span id="carousel-suggestion"></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog -->
    <section class="blog-section1" id="blog">
      <h2 class="blog-heading1" data-i18n="Our work"></h2>

      <div class="blog-list1" id="blogList">
        <article class="blog-item1" data-tags="Infraestructura">
          <a
            href="{{ asset('projects/Infraestructura-bioresponsiva/infraestructura-bioresponsiva.html') }}"
          >
            <img
              src="{{ asset('projects/Infraestructura-bioresponsiva/images/2-01.jpg') }}"
              alt="Bio-Responsive Infrastructure"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3
                class="blog-title1"
                data-i18n="Bio-Responsive Infrastructure"
              ></h3>
              <p class="blog-date1" data-i18n="Publicado"></p>
            </div>
          </a>
        </article>

        <article class="blog-item1" data-tags="Movilidad">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('projects/ViviendaModular/images/electromovilidad.png') }}"
              alt="Post 1"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3 class="blog-title1" data-i18n="Electromobility"></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>

        <article class="blog-item1" data-tags="Resiliencia">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('projects/ViviendaModular/images/Generated Image September 09, 2025 - 2_24PM.png') }}"
              alt="Post 1"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3 class="blog-title1" data-i18n="Urban Heat Islands"></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>
        <article class="blog-item1" data-tags="Urbanismo">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('img/1x/Mesa de trabajo 2.png') }}"
              alt="Transit-Oriented Development"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3
                class="blog-title1"
                data-i18n="Transit-Oriented Development"
              ></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>

        <article class="blog-item1" data-tags="Tecnología">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('img/1x/Mesa de trabajo 3.png') }}"
              alt="Computer Vision for Urban Life"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3
                class="blog-title1"
                data-i18n="Computer Vision for Urban Life"
              ></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>

        <article class="blog-item1" data-tags="Tecnología">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('img/1x/Mesa de trabajo 1.png') }}"
              alt="Augmented Reality"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3 class="blog-title1" data-i18n="Augmented Reality"></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>

        <article class="blog-item1" data-tags="Vivienda">
          <a href="{{ url('Coming_Soom.html') }}">
            <img
              src="{{ asset('projects/ViviendaModular/images/11viviendamodular-01.png') }}"
              alt="Post 1"
              class="blog-thumb"
            />
            <div class="blog-info1">
              <h3 class="blog-title1" data-i18n="Modular Housing"></h3>
              <p class="blog-date1" data-i18n="December 2024"></p>
            </div>
          </a>
        </article>
      </div>

      <!-- Mensaje si no hay resultados -->
      <p id="noResults" style="display: none; color: #999; margin-top: 1em">
        No results found
      </p>
    </section>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("[data-svg]").forEach((el) => {
          const svgPath = el.getAttribute("data-svg");
          fetch(svgPath)
            .then((res) => res.text())
            .then((svgContent) => {
              el.innerHTML = svgContent;
            })
            .catch((err) => console.error("Error cargando SVG:", err));
        });
      });
    </script>

    <!-- Logo -->
    <a href="{{ route('home') }}">
      <div class="secundary-brand"></div>
    </a>
    <script>
      const container = document.querySelector(".secundary-brand");
      fetch("{{ asset('svg/indi-lab_Vertical_Animate.svg') }}")
        .then((response) => response.text())
        .then((svgText) => {
          container.innerHTML = svgText;
        })
        .catch((err) => console.error("Error cargando SVG:", err));
    </script>

    @include('partials.newsletter')
    <script src="{{ asset('js/carousel.js') }}"></script>
    <script src="{{ asset('js/cookie-consent.js') }}"></script>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
@endpush
