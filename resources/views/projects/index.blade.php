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
        <img src="{{ asset('img/What we do/pexels-gabo-orozco-lucio-233483298-28764098.jpg') }}" class="carousel-img active" />
        <img src="{{ asset('img/What we do/pexels-susan-flores-232226967-12294911.jpg') }}" class="carousel-img" />
        <img src="{{ asset('img/What we do/pexels-victor-armas-262050668-12930992.jpg') }}" class="carousel-img" />
      </div>

      <div class="carousel-content">
        <div class="hero-text-wrapper">
            <h1 class="main-title" data-i18n="menu projects">Nuestro Trabajo</h1>
            <p class="hero-subtitle" data-i18n="carrusel indi lab"></p>
        </div>

        <!-- Search Line -->
        <div class="search-line">
          <label for="carousel-search" data-i18n="INDI Lab #"></label>
          <div class="input-wrapper">
            <input id="carousel-search" type="text" autocomplete="off" placeholder="Buscar..." />
            <span id="carousel-suggestion"></span>
          </div>
        </div>
        
        @auth
        <div style="margin-top: 30px;">
             <a href="{{ route('work.create') }}" class="admin-add-btn">+ Agregar Nuevo Proyecto</a>
        </div>
        @endauth
      </div>
    </section>

    <!-- Projects Grid -->
    <section class="blog-section1" id="blog">
      <div class="section-header">
        <h2 class="blog-heading1" data-i18n="Our work">Nuestro Trabajo</h2>
        <div class="section-underline"></div>
      </div>

      <div class="blog-list1" id="blogList">
        @forelse($projects as $project)
            <x-project-card :project="$project" :showEdit="true" />
        @empty
            <div class="empty-state">
                <p>No se encontraron proyectos. <a href="{{ route('work.create') }}">¿Crear uno?</a></p>
            </div>
        @endforelse
      </div>

      <p id="noResults" style="display: none; color: #999; margin-top: 2em; text-align: center;">
        No se encontraron resultados
      </p>
    </section>

    <style>
        .hero-text-wrapper { text-align: center; margin-bottom: 20px; }
        .main-title { font-size: 3.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 5px; color: #fff; text-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .hero-subtitle { font-size: 1.2rem; max-width: 600px; margin: 0 auto; opacity: 0.9; }
        .admin-add-btn { color: white; text-decoration: none; background: #e2462b; padding: 10px 20px; border-radius: 50px; font-weight: 700; transition: all 0.3s; box-shadow: 0 4px 15px rgba(226, 70, 43, 0.4); }
        .admin-add-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(226, 70, 43, 0.6); }
        
        .section-header { text-align: center; margin-bottom: 50px; }
        .section-underline { width: 60px; height: 4px; background: #e2462b; margin: 15px auto 0; border-radius: 2px; }
        
        .blog-item1 { position: relative; overflow: visible; }
        .project-card-inner { display: block; height: 100%; text-decoration: none; color: inherit; background: #1a1a1a; border-radius: 16px; overflow: hidden; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(255,255,255,0.05); }
        .project-card-inner:hover { transform: translateY(-10px); border-color: rgba(255,255,255,0.2); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        
        .blog-thumb-wrapper { position: relative; overflow: hidden; }
        .blog-thumb { transition: transform 0.6s ease; }
        .project-card-inner:hover .blog-thumb { transform: scale(1.05); }
        
        .blog-category-badge { position: absolute; top: 15px; left: 15px; z-index: 5; }
        
        .blog-info1 { padding: 20px; }
        .blog-title1 { font-size: 1.4rem; margin-bottom: 15px; color: #fff; }
        .blog-meta { display: flex; justify-content: space-between; align-items: center; }
        
        .btn-edit-trigger { background: transparent; border: 1px solid #e2462b; color: #e2462b; padding: 6px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-edit-trigger:hover { background: #e2462b; color: #fff; }
    </style>

    {{-- Rest of the scripts and logo --}}
    <a href="{{ route('home') }}">
      <div class="secundary-brand"></div>
    </a>
    
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
