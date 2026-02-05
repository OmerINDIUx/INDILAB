@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title . ' | INDI Lab')
@section('meta_description', $project->meta_description ?? $project->short_description ?? 'INDI Lab Project')

@section('html-class', ($project->theme ?? 'dark') == 'light' ? 'light-theme' : 'dark-theme')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style-global-blog.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/link-styles.css') }}" />

    <style>
        :root {
            --cms-theme-bg: {{ ($project->theme ?? 'dark') == 'light' ? '#eeeeee' : '#1a1a1a' }};
            --cms-theme-text: {{ ($project->theme ?? 'dark') == 'light' ? '#1a1a1a' : '#eeeeee' }};
        }
        
        /* Layout Fixes & Forced Visibility */
        body {
            padding: 0 !important;
            background-color: var(--cms-theme-bg) !important;
            color: var(--cms-theme-text) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        main {
            position: relative;
            z-index: 10;
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
        }

        /* Ensure texts are visible even if translation fails */
        [data-i18n]:empty::before {
            content: attr(data-i18n);
            opacity: 0.5;
            font-size: 0.8em;
        }

        @media (max-width: 768px) {
            body { padding: 0 !important; }
        }

        .sticky-header-clone { 
            background-color: var(--cms-theme-bg); 
            color: var(--cms-theme-text); 
            z-index: 9000;
        }

        /* Progress Bar */
        #read-progress-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            z-index: 9501;
            pointer-events: none;
        }
        #read-progress-bar {
            height: 100%;
            width: 0%;
            background: #18b2e8; /* Blue progress bar */
            box-shadow: 0 0 10px rgba(24, 178, 232, 0.5);
        }
        
        .blog-scroll-strip__rail { min-height: 100vh; overflow: visible; }
        
        .carousel-item.card-item {
            background: {{ ($project->theme ?? 'dark') == 'light' ? 'rgba(0,0,0,0.05)' : '#252525' }};
        }
    </style>
@endpush

@section('content')

<script>
    document.documentElement.classList.remove('light-theme', 'dark-theme');
    document.documentElement.classList.add('{{ ($project->theme ?? "dark") }}-theme');
</script>

@auth
<div style="position: fixed; bottom: 30px; left: 30px; z-index: 99999;">
    <button type="button" 
        onclick="handleEditClick(this)"
        data-has-draft="{{ $project->draft_content ? 'true' : 'false' }}"
        data-draft-date="{{ $project->draft_updated_at ? $project->draft_updated_at->format('d/m/Y H:i') : '' }}"
        data-draft-user="{{ $project->lastEditor ? $project->lastEditor->name : 'Sistema' }}"
        data-edit-url="{{ route('work.edit', $project) }}"
        style="background: rgb(226, 70, 43); color: #fff; border: none; padding: 12px 24px; border-radius: 30px; text-decoration: none; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 10px 20px rgba(0,0,0,0.2); cursor: pointer;">
        <i class="fas fa-edit"></i> Edit Project
    </button>
</div>

{{-- Recovery Modal --}}
<div id="recovery-modal" class="cms-modal-overlay" style="display: none;">
    <div class="cms-modal">
        <i class="fas fa-file-signature" style="font-size: 3rem; color: #1a1a1a; margin-bottom: 20px; display: block;"></i>
        <h3>Recuperar Borrador</h3>
        <p>Hemos detectado cambios guardados que aún no se han publicado. ¿Cómo te gustaría continuar?</p>
        
        <div class="draft-meta">
            <div><i class="far fa-calendar-alt"></i> <strong>Fecha:</strong> <span id="modal-draft-date"></span></div>
            <div><i class="far fa-user"></i> <strong>Usuario:</strong> <span id="modal-draft-user"></span></div>
        </div>

        <div class="modal-actions">
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
    .cms-modal .draft-meta {
        background: #f8f8f8; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #eee; text-align: left; color: #1a1a1a;
    }
    .cms-modal .modal-actions { display: flex; gap: 15px; justify-content: center; }
    .cms-modal .modal-actions button {
        padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;
        transition: all 0.2s; border: none; font-size: 1rem; flex: 1;
    }
    .cms-modal .btn-primary { background: #1a1a1a; color: #fff; }
    .cms-modal .btn-secondary { background: #eee; color: #333; }
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
    function openDraft() { window.location.href = currentEditUrl + (currentEditUrl.includes('?') ? '&' : '?') + 'use_draft=1'; }
    function openLive() { window.location.href = currentEditUrl; }
    function closeRecoveryModal() { document.getElementById('recovery-modal').style.display = 'none'; }
</script>
@endauth

{{-- Progress Bar Container --}}
<div id="read-progress-container">
    <div id="read-progress-bar"></div>
</div>

<h3 id="sticky-header-clone" class="sticky-header-clone">
    @if(isset($project->content['blocks']))
        @foreach($project->content['blocks'] as $block)
            @if($block['type'] === 'hero')
                {{ $block['data']['h3'] ?? $project->title }}
            @endif
        @endforeach
    @endif
</h3>

<main>
    @if(isset($project->content['blocks']) && is_array($project->content['blocks']))
        @php $runCounter = 1; @endphp
        @foreach($project->content['blocks'] as $block)
            @php $data = $block['data'] ?? []; @endphp

            {{-- 1. HERO (Static or Sequence) --}}
            @if($block['type'] === 'hero')
                @if(($project->hero_type ?? 'static') === 'sequence' && $project->hero_folder_id)
                    <!-- Sequence Hero Layer -->
                    <div id="hero-sequence-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0; pointer-events: none; overflow: hidden;">
                        <canvas id="hero-sequence-canvas" style="width: 100%; height: 100%; object-fit: cover;"></canvas>
                    </div>
                @else
                    <!-- Fixed Background Layer (Static) -->
                    <div class="fixed-hero-bg" style="
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100vh;
                        z-index: 0;
                        background-image: url('{{ isset($data['image']) ? asset('storage/' . $data['image']) : '' }}'); 
                        background-size: cover; 
                        background-position: center;
                        pointer-events: none;">
                    </div>
                @endif

                <!-- Title Section (Scrolls over Fixed BG/Canvas) -->
                <section class="title-section-video {{ ($project->hero_type ?? 'static') === 'sequence' ? 'hero-sequence-trigger' : '' }}" style="
                    height: 100vh; 
                    margin-top: 0 !important; 
                    padding-top: 0; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    background: transparent; 
                    position: relative;
                    z-index: 10;">
                    <div class="Title">
                        @if(!empty($data['h3']))
                            <h3 class="general-tittle">{!! $data['h3'] !!}</h3>
                        @endif
                        <h1>{!! $data['h1'] ?? $project->title !!}</h1>
                        @if(!empty($data['h2']))
                            <h2>{!! $data['h2'] !!}</h2>
                        @endif
                    </div>
                </section>
            @endif

            {{-- 2. INTRO GLASS --}}
            @if($block['type'] === 'intro_glass')
            <section>
                <div class="card">
                    <p>{!! $data['text'] ?? '' !!}</p>
                </div>
            </section>
            @endif
            {{-- 3. STATEMENT (Animated Corners) --}}
            @if($block['type'] === 'statement')
            <section class="statement">
                <div class="content-text">
                    <h2>{{ $data['text'] ?? '' }}</h2>
                    <!-- Inline SVGs -->
                    <div class="corner corner-bottom-left">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1920" style="enable-background: new 0 0 1920 1920" xml:space="preserve">
                            <g fill="#eee" class="corner_booton_left"><path d="M1,1919L1,1l448.2,0l0,1400.1c0,63-29.9,131.5-73.3,175.4l2.7,2.7c43.5-43.8,111.4-74,173.8-74H1919V1919L1,1919z"/></g>
                        </svg>
                    </div>
                    <div class="corner corner-top-right">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1920" style="enable-background: new 0 0 1920 1920" xml:space="preserve">
                            <g fill="#eee" class="corner_top_right"><path d="M1920,0l0,1918l-448.2,0l0-1400.1c0-63,29.9-131.5,73.3-175.4l-2.7-2.7c-43.5,43.8-111.4,74-173.8,74L2,413.7L2,0L1920,0z"/></g>
                        </svg>
                    </div>
                </div>
            </section>
            @endif

            {{-- 4. PROVOCATION (Image + Text Grid Layout) --}}
            @if($block['type'] === 'text_provocation')
            <section class="text-provocation">
                @if(!empty($data['image']))
                <img src="{{ asset('storage/' . $data['image']) }}" alt="Provocation Image">
                @endif
                <div class="provoc-text">
                    <h2>{{ $data['text'] ?? '' }}</h2>
                </div>
            </section>
            @endif

            {{-- 4.5. CUSTOM HTML (User-injected HTML/JS) --}}
            @if($block['type'] === 'custom_html')
            {!! $data['html'] ?? '' !!}
            @endif

            {{-- 5. PHRASE (Aligned with .blog-scroll-strip__phrase) --}}
            @if($block['type'] === 'phrase')
            <section class="blog-scroll-strip-phrase-section">
                <div class="blog-scroll-strip__phrase">
                    <h2>{{ $data['text'] ?? '' }}</h2>
                </div>
            </section>
            @endif

            {{-- 4. TEXT LARGE (Refactored to match reference structure) --}}
            @if($block['type'] === 'text_large')
            <section class="TextLarge">
                <div>
                    @php
                        $elements = $data['elements'] ?? [];
                        // Migration/Backward Compatibility
                        if (empty($elements) && (isset($data['h2']) || isset($data['content']))) {
                            if (!empty($data['h2'])) $elements[] = ['type' => 'h2', 'value' => $data['h2']];
                            if (!empty($data['content'])) $elements[] = ['type' => 'text', 'value' => $data['content']];
                        }
                    @endphp
                    @foreach($elements as $element)
                        @if(($element['type'] ?? '') === 'h2')
                            <h2>{!! $element['value'] ?? '' !!}</h2>
                        @else
                            <div>{!! $element['value'] ?? '' !!}</div>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif

            {{-- 5. SCROLL STRIP (blog-scroll-strip) - Split Logic per reference --}}
            @if($block['type'] === 'gallery_rail' && !empty($data['images']))
                @php
                    $images = $data['images'];
                    $count = count($images);
                    $phrase = $data['phrase'] ?? null;
                    // Reference requires 4-8 images + phrase for full effect.
                    // If phrase exists and we have enough images, we split them into run1 and run2 to wrap the phrase.
                    $doSplit = !empty($phrase) && $count >= 4;
                    
                    $imgs1 = $doSplit ? array_slice($images, 0, ceil($count/2)) : $images;
                    $imgs2 = $doSplit ? array_slice($images, ceil($count/2)) : [];
                @endphp

                {{-- PHRASE SECTION (Pinned) --}}
                @if($doSplit && $phrase)
                <section class="blog-scroll-strip-phrase-section">
                    <div class="blog-scroll-strip__phrase">
                        <h2>{{ $phrase }}</h2>
                    </div>
                </section>
                @endif

                {{-- RUN 1 --}}
                <section class="blog-scroll-strip">
                    <div class="blog-scroll-strip__inner">
                        <div class="blog-scroll-strip__rail">
                            @foreach($imgs1 as $img)
                                @if(is_string($img))
                                <figure class="blog-scroll-card">
                                    <img src="{{ asset('storage/' . $img) }}" />
                                </figure>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- RUN 2 --}}
                @if($doSplit && count($imgs2) > 0)
                <section class="blog-scroll-strip">
                    <div class="blog-scroll-strip__inner">
                        <div class="blog-scroll-strip__rail">
                            @foreach($imgs2 as $img)
                                @if(is_string($img))
                                <figure class="blog-scroll-card">
                                    <img src="{{ asset('storage/' . $img) }}" />
                                </figure>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
            @endif

            {{-- 6. CAROUSEL ADV (Custom Horizontal Section) --}}
            @if($block['type'] === 'carousel_adv' && !empty($data['slides']))
            <section class="horizontal-scroll-section">
                <div class="carousel-wrapper">
                    @foreach($data['slides'] as $slide)
                    <div class="carousel-item card-item">
                        @if(!empty($slide['image']))
                            <img src="{{ asset('storage/' . $slide['image']) }}" class="carrucel-imagen" />
                        @endif
                        <h2 style="grid-area: title;">
                            @if(!empty($slide['link']))
                                <a href="{{ $slide['link'] }}" target="_blank">{!! $slide['title'] ?? '' !!}</a>
                            @else
                                {!! $slide['title'] ?? '' !!}
                            @endif
                        </h2>
                        <div style="grid-area: text; font-size: 0.95rem; margin: 0; line-height: 1.5;">{!! $slide['description'] ?? '' !!}</div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        @endforeach
    @else
        <div style="padding: 100px; text-align: center;">
            <h2>Este proyecto aún no tiene contenido.</h2>
            <p>Usa el editor para añadir bloques.</p>
        </div>
    @endif
</main>

@include('partials.newsletter')

@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/demo4/traduction.js') }}"></script>
    <script src="{{ asset('js/scroll-strip.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap === 'undefined') {
                console.error("GSAP not loaded");
                return;
            }
            
            gsap.registerPlugin(ScrollTrigger);

            const header = document.getElementById('sticky-header-clone');
            if (header) {
                ScrollTrigger.create({
                    trigger: ".Title",
                    start: "50% top", // Appears when 50% of Title is out
                    onEnter: () => header.classList.add('visible'),
                    onLeaveBack: () => header.classList.remove('visible'),
                });
            }

            document.querySelectorAll('.horizontal-scroll-section').forEach(section => {
                const wrapper = section.querySelector('.carousel-wrapper');
                if (!wrapper) return;
                
                const scrollAmount = wrapper.scrollWidth - window.innerWidth;
                
                gsap.to(wrapper, {
                    x: -scrollAmount - 100,
                    ease: "none",
                    scrollTrigger: {
                        trigger: section, pin: true, scrub: 1, invalidateOnRefresh: true,
                        end: () => "+=" + (scrollAmount + 500)
                    }
                });
            });

            // CORNER ANIMATION (Statement Block)
            function initCornerAnimation() {
                const statement = document.querySelector(".statement");
                const bottomLeft = document.querySelector(".corner-bottom-left");
                const topRight = document.querySelector(".corner-top-right");
                const content = document.querySelector(".statement .content-text");

                if (!statement || !bottomLeft || !topRight || !content) return;

                function getPositions() {
                    const w = content.offsetWidth;
                    const h = content.offsetHeight;
                    const cornerSize = bottomLeft.offsetWidth || 50;
                    
                    // Start Positions (Expanded outwards)
                    const startX_BL = (w / 2) + 100;
                    const startY_BL = -(h / 2) - 100;

                    const startX_TR = -(w / 2) - 100;
                    const startY_TR = (h / 2) + 100;

                    return {
                        bottomLeft: { startX: startX_BL, startY: startY_BL },
                        topRight: { startX: startX_TR, startY: startY_TR },
                    };
                }

                const tl = gsap.timeline({
                    scrollTrigger: {
                        trigger: ".statement",
                        start: "top center",
                        end: "bottom center",
                        scrub: 1,
                        invalidateOnRefresh: true,
                    },
                });

                tl.fromTo(bottomLeft, 
                    { x: () => getPositions().bottomLeft.startX, y: () => getPositions().bottomLeft.startY, opacity: 0 },
                    { x: 0, y: 0, opacity: 1, ease: "power2.out" }
                ).fromTo(topRight, 
                    { x: () => getPositions().topRight.startX, y: () => getPositions().topRight.startY, opacity: 0 },
                    { x: 0, y: 0, opacity: 1, ease: "power2.out" }, "<"
                );
                
                // Fade in text with cleaner easing
                 gsap.fromTo(".statement .content-text", 
                    { opacity: 0, y: 30 },
                    { 
                        opacity: 1, 
                        y: 0, 
                        duration: 1.5, 
                        ease: "power3.out",
                        scrollTrigger: { 
                            trigger: ".statement", 
                            start: "top 75%",
                            toggleActions: "play none none reverse" 
                        } 
                    }
                );
            }
            initCornerAnimation();
            
            // SEQUENCE HERO LOGIC
            @if(($project->hero_type ?? 'static') === 'sequence' && $project->hero_folder_id)
            initHeroSequence();
            @endif

            function initHeroSequence() {
                const canvas = document.getElementById("hero-sequence-canvas");
                if(!canvas) return;
                const context = canvas.getContext("2d");
                
                const images = [];
                const imageSeq = { frame: 0 };
                const mediaPaths = {!! json_encode($heroImages->pluck('path')) !!};
                
                if(mediaPaths.length === 0) return;
                
                // Pre-load images
                let loadedCount = 0;
                mediaPaths.forEach((path, i) => {
                    const img = new Image();
                    img.onload = () => {
                        loadedCount++;
                        if(loadedCount === 1) render(); // Render first frame immediately
                    };
                    img.src = (window.rootPath || '/') + 'storage/' + path;
                    images.push(img);
                });

                // Setup GSAP Animation
                gsap.to(imageSeq, {
                    frame: mediaPaths.length - 1,
                    snap: "frame",
                    ease: "none",
                    scrollTrigger: {
                        trigger: ".hero-sequence-trigger",
                        start: "top top",
                        endTrigger: ".card", // Fade out or sequence ends near first real content block
                        end: "top top",
                        scrub: 1,
                        onUpdate: render
                    }
                });



                function resizeCanvas() {
                    canvas.width = canvas.clientWidth;
                    canvas.height = canvas.clientHeight;
                    render();
                }

                function render() {
                    const img = images[imageSeq.frame];
                    if (!img || !img.complete) return;

                    const cw = canvas.width;
                    const ch = canvas.height;
                    const iw = img.width;
                    const ih = img.height;

                    const baseScale = Math.max(cw / iw, ch / ih);
                    const scale = baseScale * 1.05;

                    const drawWidth = iw * scale;
                    const drawHeight = ih * scale;
                    const offsetX = (cw - drawWidth) / 2;
                    const offsetY = (ch - drawHeight) / 2;

                    context.clearRect(0, 0, cw, ch);
                    context.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);
                }

                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();
            }

            // Progress Bar Animation
            gsap.to("#read-progress-bar", {
                width: "100%",
                ease: "none",
                scrollTrigger: {
                    trigger: "body",
                    start: "top top",
                    end: "bottom bottom",
                    scrub: true
                }
            });

            // Replicated animations for cards and text
            gsap.utils.toArray('.card, .blog-scroll-strip__phrase, .text-content').forEach(el => {
                gsap.from(el, { y: 80, opacity: 0, duration: 1.5, ease: "power3.out", scrollTrigger: { trigger: el, start: "top 95%" } });
            });
        });
    </script>
@endpush
