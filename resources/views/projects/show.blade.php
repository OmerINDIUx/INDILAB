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
            padding-left: 100px !important;
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
            body { padding-left: 0 !important; }
        }

        .sticky-header-clone { 
            background-color: var(--cms-theme-bg); 
            color: var(--cms-theme-text); 
            z-index: 9000;
        }
        
        .Title { 
            padding: 0 5vw; 
            max-width: 1200px; 
            margin: 4vh auto; 
            text-align: center;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .carousel-item.card-item {
            display: grid !important;
            grid-template-columns: 35% 1fr;
            grid-template-areas: "image title" "image text";
            gap: 2rem;
            width: 85vw;
            max-width: 1400px;
            background: {{ ($project->theme ?? 'dark') == 'light' ? 'rgba(0,0,0,0.05)' : '#252525' }};
        }

        /* TextLarge & Related Styles - EXACT COPY from style-global-blog.css */
        .TextLarge {
            text-align: center;
            font-size: 1.2rem;
            color: #1a1a1a;
            background-color: #eeeeee;
            padding-top: 0;
            position: relative;
            overflow: hidden;
            padding-bottom: 3rem;
        }
        
        .TextLarge div { 
            max-width: 80%; 
            margin: 0 auto; 
            text-align: justify !important; 
            color: #1a1a1a;
            line-height: 1.8;
            max-width: 70%;
        }

        .TextLarge h2 {
            text-align: left;
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.2rem;
            max-width: 70%;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .span-resaltado {
            display: block;
            margin: 2.5rem 0;
            padding-left: 1.5rem;
            border-left: 4px solid #1a1a1a;
            color: #1a1a1a;
            font-size: 1.5rem;
            font-weight: 500;
            font-style: italic;
            line-height: 1.4;
            background: linear-gradient(
                90deg,
                rgba(26, 26, 26, 0.05) 0%,
                rgba(255, 255, 255, 0) 100%
            );
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-radius: 0 8px 8px 0;
        }

        .span-blanco {
            color: #eeeeee !important;
            border-left: 4px solid #eee;
            font-size: 1rem;
            padding-left: 1rem;
        }

        @media (max-width: 1024px) {
            .TextLarge div { max-width: 90%; }
            .TextLarge h2 { max-width: 90%; }
        }


        .blog-scroll-strip__rail { min-height: 100vh; overflow: visible; }

        @media (max-width: 768px) {
            .carousel-item.card-item {
                grid-template-columns: 1fr;
                grid-template-areas: "image" "title" "text";
                width: 90vw;
            }
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
    <a href="{{ route('work.edit', $project) }}" style="background: rgb(226, 70, 43); color: #fff; padding: 12px 24px; border-radius: 30px; text-decoration: none; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
        <i class="fas fa-edit"></i> Edit Project
    </a>
</div>
@endauth

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

            {{-- 1. HERO (Refactored: Fixed BG + Scrolling Title) --}}
            @if($block['type'] === 'hero')
            <!-- Fixed Background Layer (Behind everything) -->
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

            <!-- Title Section (Scrolls over Fixed BG) -->
            <section class="title-section-video" style="
                height: 100vh; 
                margin-top: 0 !important; 
                padding-top: 0; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                background: transparent; /* No background here */
                position: relative;
                z-index: 10; /* Above fixed bg */">
                <div class="Title">
                    @if(!empty($data['h3']))
                        <h3 class="general-tittle">{{ $data['h3'] }}</h3>
                    @endif
                    <h1>{{ $data['h1'] ?? $project->title }}</h1>
                    @if(!empty($data['h2']))
                        <h2>{{ $data['h2'] }}</h2>
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

            {{-- 3. PHRASE (Aligned with .blog-scroll-strip__phrase) --}}
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
                    @if(!empty($data['h2']))
                        <h2>{{ $data['h2'] }}</h2>
                    @endif
                    {!! $data['content'] ?? '' !!}
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
                                <a href="{{ $slide['link'] }}" target="_blank">{{ $slide['title'] ?? '' }}</a>
                            @else
                                {{ $slide['title'] ?? '' }}
                            @endif
                        </h2>
                        <p style="grid-area: text;">{{ $slide['description'] ?? '' }}</p>
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
                // Force visibility debug
                console.log("Sticky Header Found");
                
                ScrollTrigger.create({
                    start: "top top", // Trigger immediately at top for testing or slight scroll
                    end: "max",
                    onUpdate: (self) => {
                        // Show if scrolled more than 50px OR scrolling UP
                        if (self.scroll() > 50 && self.direction === 1) header.classList.add('visible');
                        else if (self.direction === -1) header.classList.add('visible'); // Show on scroll up
                        else if (self.scroll() < 50) header.classList.remove('visible'); // Hide at very top
                    }
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

            // Replicated animations for cards and text
            gsap.utils.toArray('.card, .blog-scroll-strip__phrase, .text-content').forEach(el => {
                gsap.from(el, { y: 80, opacity: 0, duration: 1.5, ease: "power3.out", scrollTrigger: { trigger: el, start: "top 95%" } });
            });
        });
    </script>
@endpush
