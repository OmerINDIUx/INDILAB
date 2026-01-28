@extends('layouts.app')

@section('title', 'What We Do | INDI Lab')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}" />
@endpush

@section('content')
    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin: 20px auto; max-width: 1200px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif
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
              @if($project->coming_soon)
                <div style="cursor: default; display: block; height: 100%;">
              @else
                <a href="{{ route('work.show', $project) }}">
              @endif

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
                  @if($project->category)
                    <span class="blog-category-badge {{ $project->badge_color ?? 'cat-grad-1' }}">{{ $project->category }}</span>
                  @endif
                  <h3 class="blog-title1">{{ $project->title }}</h3>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p class="blog-date1">
                        {{ $project->coming_soon ? 'PRÓXIMAMENTE' : ($project->published_at ? $project->published_at->format('F Y') : 'Draft') }}
                    </p>
                    @auth
                      <button type="button" 
                        class="btn-edit-trigger" 
                        style="background: #e2462b; color: #fff; border: none; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; cursor: pointer; text-transform: uppercase;"
                        data-id="{{ $project->slug }}"
                        data-has-draft="{{ $project->draft_content ? 'true' : 'false' }}"
                        data-draft-date="{{ $project->draft_updated_at ? $project->draft_updated_at->format('d/m/Y H:i') : '' }}"
                        data-draft-user="{{ $project->lastEditor ? $project->lastEditor->name : 'Sistema' }}"
                        data-edit-url="{{ route('work.edit', $project) }}"
                        onclick="handleEditClick(this)">
                        Editar
                      </button>
                    @endauth
                  </div>
                </div>

              @if($project->coming_soon)
                </div>
              @else
                </a>
              @endif
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
    @include('partials.newsletter')

    {{-- Recovery Modal --}}
    <div id="recovery-modal" class="cms-modal-overlay" style="display: none;">
        <div class="cms-modal">
            <i class="fas fa-file-signature" style="font-size: 3rem; color: #1a1a1a; margin-bottom: 20px; display: block;"></i>
            <h3>Recuperar Borrador</h3>
            <p>Hemos detectado cambios guardados que aún no se han publicado. ¿Cómo te gustaría continuar?</p>
            
            <div class="draft-meta" style="background: #f8f8f8; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #eee; text-align: left; color: #1a1a1a;">
                <div><i class="far fa-calendar-alt"></i> <strong>Fecha:</strong> <span id="modal-draft-date"></span></div>
                <div><i class="far fa-user"></i> <strong>Usuario:</strong> <span id="modal-draft-user"></span></div>
            </div>

            <div class="modal-actions" style="display: flex; gap: 15px; justify-content: center;">
                <button type="button" class="btn-modal btn-secondary" onclick="openLive()">Usar Versión Publicada</button>
                <button type="button" class="btn-modal btn-primary" onclick="openDraft()">Cargar Borrador</button>
            </div>
            <button type="button" onclick="closeRecoveryModal()" style="margin-top: 20px; background: none; border: none; color: #888; text-decoration: underline; cursor: pointer;">Cancelar</button>
        </div>
    </div>

    <style>
        .cms-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); backdrop-filter: blur(5px);
            display: flex; align-items: center; justify-content: center;
            z-index: 20000;
        }
        .cms-modal {
            background: #fff; color: #1a1a1a !important; padding: 40px; border-radius: 16px;
            max-width: 500px; width: 90%; text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .cms-modal h3 { font-size: 1.8rem; margin-bottom: 15px; font-weight: 800; color: #1a1a1a; }
        .cms-modal p { color: #666; margin-bottom: 30px; line-height: 1.6; }
        .cms-modal .modal-actions button {
            padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; border: none; font-size: 1rem; flex: 1;
        }
        .btn-primary { background: #1a1a1a; color: #fff; }
        .btn-secondary { background: #eee; color: #333; }
        .btn-primary:hover { background: #000; transform: translateY(-2px); }
        .btn-secondary:hover { background: #ddd; }
    </style>

    <script>
        let currentEditUrl = '';

        function handleEditClick(btn) {
            const hasDraft = btn.getAttribute('data-has-draft') === 'true';
            currentEditUrl = btn.getAttribute('data-edit-url');
            
            if (hasDraft) {
                document.getElementById('modal-draft-date').innerText = btn.getAttribute('data-draft-date');
                document.getElementById('modal-draft-user').innerText = btn.getAttribute('data-draft-user');
                document.getElementById('recovery-modal').style.display = 'flex';
            } else {
                window.location.href = currentEditUrl;
            }
        }

        function openDraft() {
            window.location.href = currentEditUrl + (currentEditUrl.includes('?') ? '&' : '?') + 'use_draft=1';
        }

        function openLive() {
            window.location.href = currentEditUrl;
        }

        function closeRecoveryModal() {
            document.getElementById('recovery-modal').style.display = 'none';
        }
    </script>

    <script src="{{ asset('js/carousel.js') }}"></script>
    <script src="{{ asset('js/cookie-consent.js') }}"></script>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
@endpush
