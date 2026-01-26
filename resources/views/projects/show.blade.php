@extends('layouts.app')

@section('title', $project->meta_title ?? $project->title . ' | INDI Lab')

@section('body-class', 'dark-theme')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/style-global-blog.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/link-styles.css') }}" />
@endpush

@section('content')

@auth
<!-- Admin Controls -->
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <a href="{{ route('work.edit', $project) }}" style="background: white; color: black; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-right: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">Edit</a>
    <form action="{{ route('work.destroy', $project) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; border: none; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.3);" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
</div>
@endauth

<!-- 1. Hero Section -->
<section class="title-section-video" style="padding-top: 150px; min-height: 60vh; display: flex; align-items: flex-end; padding-bottom: 50px; background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('{{ $project->image_path ? asset('storage/' . $project->image_path) : '' }}'); background-size: cover; background-position: center;">
    <div class="Title" style="padding: 0 5vw;">
        @if($project->subtitle)
            <h3 class="general-tittle" style="opacity: 0.9;">{{ $project->subtitle }}</h3>
        @endif
        <h1 style="font-size: 4rem; line-height: 1.1;">{{ $project->title }}</h1>
    </div>
</section>

    <!-- DYNAMIC BLOCK RENDERER -->
    @if(isset($project->content['blocks']) && is_array($project->content['blocks']))
        
        @foreach($project->content['blocks'] as $block)
            @php $data = $block['data'] ?? []; @endphp

            {{-- 1. HERO BLOCK --}}
            @if($block['type'] === 'hero')
            <section class="title-section-video" style="padding-top: 150px; min-height: 60vh; display: flex; align-items: flex-end; padding-bottom: 50px; background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('{{ isset($data['image']) ? asset('storage/' . $data['image']) : '' }}'); background-size: cover; background-position: center;">
                <div class="Title" style="padding: 0 5vw;">
                    @if(!empty($data['subtitle']))
                        <h3 class="general-tittle" style="opacity: 0.9;">{{ $data['subtitle'] }}</h3>
                    @endif
                    <h1 style="font-size: 4rem; line-height: 1.1;">{{ $data['title'] ?? '' }}</h1>
                </div>
            </section>
            @endif

            {{-- 2. INTRO TEXT BLOCK --}}
            @if($block['type'] === 'intro')
            <section class="TextLarge" style="padding-top: 80px; padding-bottom: 80px; background: #000;">
                <div style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
                    <p style="font-size: 1.5rem; line-height: 1.6; color: #ddd;">
                        {!! nl2br(e($data['text'] ?? '')) !!}
                    </p>
                </div>
            </section>
            @endif

            {{-- 3. QUOTE BLOCK --}}
            @if($block['type'] === 'quote')
            <section id="phrase-section" style="padding: 100px 0; background: #111;">
              <div class="blog-scroll-strip__phrase" style="text-align: center; max-width: 1200px; margin: 0 auto;">
                <h2 style="font-size: 3rem; font-weight: 300; letter-spacing: -1px; color: #fff;">
                    "{{ $data['text'] ?? '' }}"
                </h2>
              </div>
            </section>
            @endif

            {{-- 4. RICH TEXT SECTION --}}
            @if($block['type'] === 'text')
             <section class="TextLarge" style="padding: 80px 0; background: #000;">
              <div style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
                @if(!empty($data['title']))
                    <h2 style="font-size: 2rem; margin-bottom: 30px; letter-spacing: -0.5px;">{{ $data['title'] }}</h2>
                @endif
                <div style="font-size: 1.2rem; line-height: 1.8; color: #ccc;">
                    {!! nl2br(e($data['content'] ?? '')) !!}
                </div>
              </div>
            </section>
            @endif

            {{-- 5. GALLERY STRIP --}}
            @if($block['type'] === 'gallery' && !empty($data['images']))
            <section class="blog-scroll-strip" style="overflow: hidden; padding: 50px 0;">
              <div class="blog-scroll-strip__inner">
                <div class="blog-scroll-strip__rail" style="display: flex; gap: 20px;">
                  @foreach($data['images'] as $img)
                  <figure class="blog-scroll-card" style="min-width: 400px; height: 300px; flex-shrink: 0; margin: 0;">
                    <img src="{{ asset('storage/' . $img) }}" style="width: 100%; height: 100%; object-fit: cover;" />
                  </figure>
                  @endforeach
                </div>
              </div>
            </section>
            @endif

            {{-- 6. CAROUSEL --}}
            @if($block['type'] === 'carousel' && !empty($data['slides']))
            <section class="horizontal-scroll-section" style="padding: 100px 0; background: #111;">
              <div class="carousel-wrapper" style="display: flex; gap: 40px; overflow-x: auto; padding: 0 5vw; padding-bottom: 20px;">
                @foreach($data['slides'] as $slide)
                <div class="carousel-item card-item" style="min-width: 350px; flex-shrink: 0;">
                  @if(!empty($slide['image']))
                    <img src="{{ asset('storage/' . $slide['image']) }}" class="carrucel-imagen" style="width: 100%; height: 250px; object-fit: cover; margin-bottom: 20px;" />
                  @endif
                  <h2 style="font-size: 1.5rem; margin-bottom: 10px;">
                    @if(!empty($slide['link']))
                        <a href="{{ $slide['link'] }}" target="_blank" style="color:white; text-decoration:underline;">{{ $slide['title'] ?? '' }}</a>
                    @else
                        {{ $slide['title'] ?? '' }}
                    @endif
                  </h2>
                  <p style="color: #aaa;">{{ $slide['description'] ?? '' }}</p>
                </div>
                @endforeach
              </div>
            </section>
            @endif

        @endforeach

    @elseif(is_array($project->content))
        {{-- Fallback for the previous Tabbed structure (if mixed use exists during transition) --}}
        {{-- ... (Previous rendering logic could go here, but let's assume Migration is absolute for new projects) --}}
        
        {{-- Actually, let's keep the old logic as fallback or just migrate fully. 
             If 'blocks' key is missing, maybe it's the old structure? 
             Let's support legacy raw HTML as final fallback. 
        --}}
        <div class="project-content">
             {{-- Attempt to render old fields if they exist and no blocks --}}
             @if(!empty($project->content['intro_text']))
                <section class="TextLarge"><p>{{ $project->content['intro_text'] }}</p></section>
             @endif
             {{-- ... --}}
        </div>

    @else
        {{-- LEGACY RAW HTML --}}
        <div class="project-content">
            {!! $project->content !!}
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <script src="{{ asset('js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('js/scroll-strip.js') }}"></script>
    <script src="{{ asset('js/horizontal-scroll.js') }}"></script>
    
    <script>
       if (typeof gsap !== "undefined") {
          gsap.registerPlugin(ScrollTrigger);
          // Re-init custom scripts if necessary
       }
    </script>
@endpush
