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
        <!-- Static carousel images for now, or dynamic if we add a 'featured' flag later -->
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

        <!-- Search Line -->
        <div class="search-line">
          <label for="carousel-search" data-i18n="INDI Lab #"></label>
          <div class="input-wrapper">
            <input id="carousel-search" type="text" autocomplete="off" />
            <span id="carousel-suggestion"></span>
          </div>
        </div>
        
        <!-- Add Project Button (Only visible to Admin) -->
        <div style="margin-top: 20px;">
             <a href="{{ route('work.create') }}" style="color: white; text-decoration: underline; background: rgba(0,0,0,0.5); padding: 5px 10px; border-radius: 4px;">+ Java Add New Project</a>
        </div>
      </div>
    </section>

    <!-- Blog -->
    <section class="blog-section1" id="blog">
      <h2 class="blog-heading1" data-i18n="Our work"></h2>

      <div class="blog-list1" id="blogList">
        @forelse($projects as $project)
            <article class="blog-item1" data-tags="Infraestructura"> 
              <!-- Note: Tags are static for now. We can add a 'category' field to Project model later. -->
              <a href="{{ route('work.show', $project) }}">
                @if($project->image_path)
                    <img
                      src="{{ asset('storage/' . $project->image_path) }}"
                      alt="{{ $project->title }}"
                      class="blog-thumb"
                    />
                @else
                    <!-- Fallback image or placeholder -->
                     <img
                      src="{{ asset('img/1x/Mesa de trabajo 2.png') }}"
                      alt="{{ $project->title }}"
                      class="blog-thumb"
                    />
                @endif
                <div class="blog-info1">
                  <h3 class="blog-title1">{{ $project->title }}</h3>
                  <p class="blog-date1">
                      {{ $project->published_at ? $project->published_at->format('F Y') : 'Draft' }}
                  </p>
                </div>
              </a>
            </article>
        @empty
            <p style="color: white; padding: 20px;">No projects found. <a href="{{ route('work.create') }}" style="text-decoration: underline;">Create one?</a></p>
        @endforelse
      </div>

      <!-- Message if no results -->
      <p id="noResults" style="display: none; color: #999; margin-top: 1em">
        No results found
      </p>
    </section>

    <!-- Scripts specific to this page layout -->
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("[data-svg]").forEach((el) => {
          const svgPath = el.getAttribute("data-svg");
          fetch(svgPath)
            .then((res) => res.text())
            .then((svgContent) => {
              el.innerHTML = svgContent;
            })
            .catch((err) => console.error("Error loading SVG:", err));
        });
      });
    </script>

    <!-- Logo -->
    <a href="{{ route('home') }}">
      <div class="secundary-brand"></div>
    </a>
    <script>
      const container = document.querySelector(".secundary-brand");
      // Use asset helper for reliable path
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
