@extends('layouts.app')

@section('title', 'Create Project | INDI Lab')

@section('body-class', 'light-theme')

@section('content')
<!-- External Libs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/components/menu-header.css" />

<!-- Shared CMS Styles -->
<link rel="stylesheet" href="{{ asset('css/cms-editor.css') }}?v={{ time() }}" />
<link rel="stylesheet" href="{{ asset('css/blog.css') }}?v={{ time() }}" />
   <!--  Fix Preview Context overrides 
    /* .preview-viewport styles moved to Shadow DOM injection -->
</style>

<div class="cms-container">
    <div class="editor-main">
        <h1 style="font-weight: 900; font-size: 2.5rem; margin-bottom: 10px; letter-spacing: -0.02em;">Crear Nuevo Proyecto</h1>
        <p style="color: #888; margin-bottom: 40px;">Editor profesional (PowerGrotesk + Production Classes).</p>

        @if ($errors->any())
            <div style="background-color: #fceaea; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php $heroBlock = ['data' => []]; @endphp

        <form action="{{ route('work.store') }}" method="POST" enctype="multipart/form-data" id="projectForm">
            @csrf
            
            <div class="blocks-container">
                <div class="section-header">Configuración Global & Card Preview</div>
                <div style="display: grid; grid-template-columns: 1fr 350px; gap: 40px; align-items: start;">
                    
                    <!-- Left Column: Inputs -->
                    <div class="global-inputs">
                        <div class="form-group">
                            <label class="form-label">Título del Proyecto (Global)</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej: Infraestructura Bioresponsiva" required value="{{ old('title') }}" oninput="updateCardPreview('title', this.value)">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-control" onchange="updateCardPreview('category', this.value)">
                                <option value="">Seleccionar Categoría...</option>
                                <option value="Señales Urbanas" {{ old('category') == 'Señales Urbanas' ? 'selected' : '' }}>Señales Urbanas</option>
                                <option value="Sistemas Urbanos" {{ old('category') == 'Sistemas Urbanos' ? 'selected' : '' }}>Sistemas Urbanos</option>
                                <option value="Experimentos Urbanos" {{ old('category') == 'Experimentos Urbanos' ? 'selected' : '' }}>Experimentos Urbanos</option>
                                <option value="Urban Playbooks" {{ old('category') == 'Urban Playbooks' ? 'selected' : '' }}>Urban Playbooks</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Color del Badge</label>
                            <div style="display: flex; gap: 15px; background: #eee; padding: 10px; border-radius: 8px;">
                                @foreach(['cat-grad-1', 'cat-grad-2', 'cat-grad-3', 'cat-grad-4'] as $gClass)
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="badge_color" value="{{ $gClass }}" {{ old('badge_color', 'cat-grad-1') == $gClass ? 'checked' : '' }} onchange="updateCardPreview('badge_color', this.value)">
                                        <span class="{{ $gClass }}" style="display:inline-block; width:24px; height:24px; border-radius:50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ccc;"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Palabras Clave SEO (Meta Keywords)</label>
                            <input type="text" name="meta_keywords" class="form-control" placeholder="Ej: urbanismo, señales, interactivo" value="{{ old('meta_keywords') }}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Título Menú (Sticky)</label>
                                <input type="text" name="sticky_title" class="form-control" placeholder="Aparece al hacer scroll" value="{{ old('sticky_title') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="published_at" id="input-date" class="form-control" value="{{ old('published_at') }}" onchange="updateCardPreview('date', this.value)">
                                <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="coming_soon" id="check-coming-soon" value="1" onchange="updateCardPreview('coming_soon', this.checked)">
                                    <label for="check-coming-soon" style="font-size: 0.9rem; margin:0; cursor: pointer;">Publicar como "Próximamente"</label>
                                </div>
                            </div>
                        </div>

                        <!-- Card Image Selector -->
                        <div class="form-group">
                            <label class="form-label">Imagen de Portada (Card)</label>
                            <div class="media-selector-wrapper">
                                <div class="media-preview-box" id="preview-cover" 
                                     style="height: 100px; background-size: cover; background-position: center; border-radius: 6px; margin-bottom: 5px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                                     onclick="openMediaModal('input-cover', 'preview-cover')">
                                    <span style="font-size:0.8rem; color:#999;">Select Cover Image</span>
                                </div>
                                <input type="hidden" name="image_path" id="input-cover" value="" onchange="updateCardPreview('image', this.value)">
                                <input type="file" name="image" class="form-control" style="display:none">
                                <button type="button" class="form-control" onclick="openMediaModal('input-cover', 'preview-cover')"><i class="fas fa-image"></i> Select from Library</button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Card Preview -->
                    <div class="card-preview-container" style="background: #1a1a1a; padding: 20px; border-radius: 12px;">
                        <span style="color: #666; font-size: 0.7rem; display: block; margin-bottom: 10px; font-family: monospace;">PREVIEW: INDEX CARD</span>
                        
                        <div id="card-preview-host"></div>
                        
                        <template id="tpl-card-initial">
                            <article class="blog-item1" style="max-width: 100%; margin: 0;">
                                <a href="#" onclick="return false;" style="cursor: default;">
                                    <img src="{{ old('image_path') ? asset('storage/'.old('image_path')) : asset('img/1x/Mesa de trabajo 2.png') }}" class="blog-thumb" id="card-img-preview">
                                    <div class="blog-info1">
                                        <span class="blog-category-badge {{ old('badge_color', 'cat-grad-1') }}" id="card-category-preview" style="display:{{ old('category') ? 'inline-block' : 'none' }};">{{ old('category') }}</span>
                                        <h3 class="blog-title1" id="card-title-preview">{{ old('title', 'Título del Proyecto') }}</h3>
                                        <p class="blog-date1" id="card-date-preview">Draft</p>
                                    </div>
                                </a>
                            </article>
                        </template>
                    </div>
                </div>
                
                <!-- Hidden Theme Input (Defaulting to Dark now as requested implies removing visual toggle) -->
                {{-- Theme is handled by block styles usually, but keeping hidden input just in case controller needs it --}}
                <input type="hidden" name="theme" value="dark">
            </div>

            <!-- MANDATORY HERO (Block 0) -->
            <div class="blocks-container" style="border-left: 5px solid rgb(226, 70, 43);">
                <div class="section-header" style="color: rgb(226, 70, 43);">Hero Section (Fijo)</div>
                <input type="hidden" name="content[blocks][0][type]" value="hero">
                <div class="form-group">
                    <label class="form-label">Categoría h3</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h3', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h3-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h3', this.innerHTML)">{{ old('content.blocks.0.data.h3', $heroBlock['data']['h3'] ?? '') }}</div>
                    <input type="hidden" name="content[blocks][0][data][h3]" id="hero-h3" value="{{ old('content.blocks.0.data.h3', $heroBlock['data']['h3'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Título h1 (PowerGrotesk)</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h1', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h1-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h1', this.innerHTML)">{{ old('content.blocks.0.data.h1', $heroBlock['data']['h1'] ?? '') }}</div>
                    <input type="hidden" name="content[blocks][0][data][h1]" id="hero-h1" value="{{ old('content.blocks.0.data.h1', $heroBlock['data']['h1'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tagline h2</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h2', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h2-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h2', this.innerHTML)">{{ old('content.blocks.0.data.h2', $heroBlock['data']['h2'] ?? '') }}</div>
                    <input type="hidden" name="content[blocks][0][data][h2]" id="hero-h2" value="{{ old('content.blocks.0.data.h2', $heroBlock['data']['h2'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Imagen de Fondo Hero</label>
                    <div class="media-selector-wrapper">
                        <div class="media-preview-box" id="preview-hero" 
                             style="background-image: url('{{ isset($heroBlock['data']['image']) ? asset('storage/'.$heroBlock['data']['image']) : '' }}'); height: 200px; background-size: cover; background-position: center; border-radius: 8px; margin-bottom: 10px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                             onclick="openMediaModal('input-hero', 'preview-hero')">
                            @if(!isset($heroBlock['data']['image'])) <span style="color:#999; font-weight:600;">Click to Open Media Library</span> @endif
                        </div>
                        <input type="hidden" name="content[blocks][0][data][image]" id="input-hero" value="{{ $heroBlock['data']['image'] ?? '' }}" onchange="updateHeroBg(this.value)">
                        <input type="file" name="content[blocks][0][data][image_file]" class="form-control" style="display:none">
                        <button type="button" class="form-control" onclick="openMediaModal('input-hero', 'preview-hero')"><i class="fas fa-image"></i> Select from Library</button>
                    </div>
                </div>
            </div>

            <div id="blocks-wrapper">
                @if(old('content.blocks'))
                    @foreach(old('content.blocks') as $idx => $data)
                        @if($idx == 0) @continue @endif
                        @php $type = $data['type']; @endphp
                        <div class="block-item" data-type="{{ $type }}">
                            <input type="hidden" name="content[blocks][{{ $idx }}][type]" value="{{ $type }}">
                            <div class="block-header" onclick="toggleBlock(this)">
                                <span class="block-title"><i class="fas fa-bars"></i> {{ strtoupper(str_replace('_', ' ', $type)) }}</span>
                                <div class="block-actions"><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
                            </div>
                            <div class="block-body">
                                @if($type === 'intro_glass')
                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'b')"><b>B</b></button><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                    <div id="textarea-{{ $idx }}-editor" class="rich-editor" contenteditable="true" oninput="updateBlockPreview('{{ $idx }}', 'text', this.innerHTML)"></div>
                                    <input type="hidden" id="textarea-{{ $idx }}" name="content[blocks][{{ $idx }}][data][text]" value="{{ $data['data']['text'] ?? '' }}">

                                @elseif($type === 'text_large')
                                    <div class="form-group">
                                        <label class="form-label">Subtítulo</label>
                                        <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('h2-{{ $idx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                        <div id="h2-{{ $idx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('{{ $idx }}', 'h2', this.innerHTML)"></div>
                                        <input type="hidden" id="h2-{{ $idx }}" name="content[blocks][{{ $idx }}][data][h2]" value="{{ $data['data']['h2'] ?? '' }}">
                                    </div>
                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'b')"><b>B</b></button><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                    <div id="textarea-{{ $idx }}-editor" class="rich-editor" contenteditable="true" oninput="updateBlockPreview('{{ $idx }}', 'content', this.innerHTML)"></div>
                                    <input type="hidden" id="textarea-{{ $idx }}" name="content[blocks][{{ $idx }}][data][content]" value="{{ $data['data']['content'] ?? '' }}">
                                @elseif($type === 'gallery_rail')
                                    <div class="form-group">
                                        <label class="form-label">Frase Destacada (Scroll Strip)</label>
                                        <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                        <div id="textarea-{{ $idx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateGalleryPreview('{{ $idx }}'); document.getElementById('textarea-{{ $idx }}').value = this.innerHTML;"></div>
                                        <input type="hidden" id="textarea-{{ $idx }}" name="content[blocks][{{ $idx }}][data][phrase]" value="{{ $data['data']['phrase'] ?? '' }}">
                                    </div>
                                    <input type="file" name="content[blocks][{{ $idx }}][data][images][]" multiple class="form-control" onchange="previewGallery('{{ $idx }}', this)">
                                @elseif($type === 'carousel_adv')
                                    <div class="slides-container" id="slides-{{ $idx }}">
                                        @if(isset($data['data']['slides'])) @foreach($data['data']['slides'] as $sIdx => $slide)
                                            <div class="slide-item" style="border:1px solid #eee; padding:15px; border-radius:8px; margin-bottom:15px;">
                                                <div class="form-group">
                                                    <label class="form-label">Título</label>
                                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('slide-title-{{ $idx }}-{{ $sIdx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                                    <div id="slide-title-{{ $idx }}-{{ $sIdx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateCarouselPreview('{{ $idx }}'); document.getElementById('slide-title-{{ $idx }}-{{ $sIdx }}').value = this.innerHTML;"></div>
                                                    <input type="hidden" id="slide-title-{{ $idx }}-{{ $sIdx }}" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][title]" value="{{ $slide['title'] ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Descripción</label>
                                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('slide-desc-{{ $idx }}-{{ $sIdx }}', 'b')"><b>B</b></button><button type="button" class="rt-btn" onclick="formatText('slide-desc-{{ $idx }}-{{ $sIdx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                                    <div id="slide-desc-{{ $idx }}-{{ $sIdx }}-editor" class="rich-editor" contenteditable="true" oninput="updateCarouselPreview('{{ $idx }}'); document.getElementById('slide-desc-{{ $idx }}-{{ $sIdx }}').value = this.innerHTML;"></div>
                                                    <input type="hidden" id="slide-desc-{{ $idx }}-{{ $sIdx }}" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][description]" value="{{ $slide['description'] ?? '' }}">
                                                </div>
                                                {{-- Media selection part remains the same --}}
                                                <div class="media-selector-wrapper" style="margin-top:10px;">
                                                    <div class="media-preview-box" id="preview-{{ $idx }}-{{ $sIdx }}" 
                                                         style="background-image: url('{{ isset($slide['image']) ? asset('storage/'.$slide['image']) : '' }}'); height: 100px; background-size: cover; background-position: center; border-radius: 6px; margin-bottom: 5px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                                                         onclick="openMediaModal('input-{{ $idx }}-{{ $sIdx }}', 'preview-{{ $idx }}-{{ $sIdx }}')">
                                                        @if(!isset($slide['image'])) <span style="font-size:0.7rem; color:#999;">Select Image</span> @endif
                                                    </div>
                                                    <input type="hidden" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][image]" id="input-{{ $idx }}-{{ $sIdx }}" value="{{ $slide['image'] ?? '' }}" onchange="updateCarouselPreview('{{ $idx }}')">
                                                    <input type="file" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][image_file]" class="form-control" style="display:none">
                                                    <button type="button" class="form-control" style="padding:5px; font-size:0.8rem;" onclick="openMediaModal('input-{{ $idx }}-{{ $sIdx }}', 'preview-{{ $idx }}-{{ $sIdx }}')">Change Image</button>
                                                </div>
                                            </div>
                                        @endforeach @endif
                                    </div>
                                    <button type="button" class="form-control" onclick="addCarouselSlide('{{ $idx }}')">+ Slide</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="blocks-container" style="background: #f8f8f8; border: 2px dashed #ddd;">
                <div class="section-header">Añadir Bloque de Producción</div>
                <div class="add-block-grid">
                    <div class="add-btn-card" id="btn-add-intro_glass" onclick="addBlock('intro_glass')"><i class="fas fa-certificate"></i><span>Intro Glass</span></div>
                    <div class="add-btn-card" onclick="addBlock('statement')"><i class="fas fa-quote-right"></i><span>Statement</span></div>
                    <div class="add-btn-card" onclick="addBlock('text_provocation')"><i class="fas fa-image"></i><span>Provocation</span></div>
                    <div class="add-btn-card" onclick="addBlock('custom_html')"><i class="fas fa-code"></i><span>Custom HTML</span></div>
                    <div class="add-btn-card" onclick="addBlock('text_large')"><i class="fas fa-align-justify"></i><span>Texto Largo</span></div>
                    <div class="add-btn-card" onclick="addBlock('gallery_rail')"><i class="fas fa-layer-group"></i><span>Scroll Strip</span></div>
                    <div class="add-btn-card" onclick="addBlock('carousel_adv')"><i class="fas fa-columns"></i><span>Carousel Grid</span></div>
                </div>
            </div>
            <input type="hidden" name="action" id="form-action" value="publish">
        </form>
    </div>

    <aside class="preview-sidebar">
        <div class="preview-header"><span>HIGH-FIDELITY PREVIEW</span><div style="width: 8px; height: 8px; background: #0f0; border-radius: 50%;"></div></div>
        <div class="preview-viewport" id="preview-viewport"></div> <!-- Content injected via Shadow DOM -->
        
        <template id="initial-preview-html">
            <div id="preview-blocks-list">
                <div class="preview-item" id="prev-block-0" data-type="hero">
                    <div class="Title" id="hero-preview-bg" style="background-image: url('{{ isset($heroBlock['data']['image']) ? asset('storage/'.$heroBlock['data']['image']) : '' }}'); background-size: cover; background-position: center; border-radius: 12px; margin-bottom: 20px; min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 20px;">
                        <span class="h3 blog-category-badge {{ old('badge_color', 'cat-grad-1') }}" style="margin-bottom: 20px;">{{ $heroBlock['data']['h3'] ?? old('content.blocks.0.data.h3', 'Categoría') }}</span>
                        <h1>{{ $heroBlock['data']['h1'] ?? old('content.blocks.0.data.h1', 'TÍTULO') }}</h1>
                        <h2>{{ $heroBlock['data']['h2'] ?? old('content.blocks.0.data.h2', 'TAGLINE') }}</h2>
                    </div>
                </div>
                @if(old('content.blocks'))
                    @foreach(old('content.blocks') as $idx => $block)
                        @if($idx == 0) @continue @endif
                        <div class="preview-item" id="prev-block-{{ $idx }}" data-type="{{ $block['type'] }}">
                            @if($block['type'] === 'intro_glass') <div class="card" style="padding: 2rem;">{!! $block['data']['text'] ?? '' !!}</div>

                            @elseif($block['type'] === 'text_large') <div class="TextLarge" style="padding: 20px;"><h2>{{ $block['data']['h2'] ?? '' }}</h2><div>{!! $block['data']['content'] ?? '' !!}</div></div>
                            @elseif($block['type'] === 'gallery_rail') <div class="horizontal-scroll-section" style="overflow-x: auto; white-space: nowrap; padding: 10px;">@if(isset($block['data']['images'])) @foreach($block['data']['images'] as $img) <img src="{{ asset('storage/'.$img) }}" style="height: 120px; border-radius: 8px; margin-right: 10px; display: inline-block;"> @endforeach @endif</div>
                            @elseif($block['type'] === 'carousel_adv') <div class="carousel-wrapper-preview" style="display:flex; flex-direction:column; gap:15px;">@if(isset($block['data']['slides'])) @foreach($block['data']['slides'] as $slide) <div class="carousel-item card-item"><img src="{{ isset($slide['image']) ? asset('storage/'.$slide['image']) : '' }}" class="carrucel-imagen"><h2>{{ $slide['title'] ?? '' }}</h2><p>{{ $slide['description'] ?? '' }}</p></div> @endforeach @endif</div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </template>

        <div class="publish-area" style="display:flex; gap:10px; flex-direction:column;">
            <button type="button" class="btn-publish" style="background:#ccc; color:#333;" onclick="document.getElementById('form-action').value='save'; document.getElementById('projectForm').submit()">Guardar (Borrador)</button>
            <button type="button" class="btn-publish" onclick="document.getElementById('form-action').value='publish'; document.getElementById('projectForm').submit()">Publicar Proyecto</button>
        </div>
    </aside>
</div>

<!-- TEMPLATES -->
<template id="tpl-intro_glass">
    <div class="block-item" data-type="intro_glass"><input type="hidden" name="content[blocks][INDEX][type]" value="intro_glass">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-bars"></i> INTRO GLASS</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-INDEX', 'b')"><b>B</b></button><button type="button" class="rt-btn" onclick="formatText('textarea-INDEX', 'a')"><i class="fas fa-link"></i></button></div>
            <div id="textarea-INDEX-editor" class="rich-editor" contenteditable="true" oninput="updateBlockPreview('INDEX', 'text', this.innerHTML)"></div>
            <input type="hidden" id="textarea-INDEX" name="content[blocks][INDEX][data][text]">
        </div>
    </div>
</template>
<template id="tpl-statement">
    <div class="block-item" data-type="statement"><input type="hidden" name="content[blocks][INDEX][type]" value="statement">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-quote-right"></i> STATEMENT (ANIMATED)</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Texto del Statement</label>
                <textarea class="form-control" name="content[blocks][INDEX][data][text]" rows="3" oninput="updateBlockPreview('INDEX', 'statement', this.value)"></textarea>
            </div>
        </div>
    </div>
</template>
<template id="tpl-text_provocation">
    <div class="block-item" data-type="text_provocation"><input type="hidden" name="content[blocks][INDEX][type]" value="text_provocation">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-image"></i> PROVOCATION (IMG + TEXT)</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Texto de Provocación (H2)</label>
                <textarea class="form-control" name="content[blocks][INDEX][data][text]" rows="2" oninput="updateBlockPreview('INDEX', 'text_provocation', this.value)"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Imagen de Fondo</label>
                <div class="media-selector-wrapper">
                    <div class="media-preview-box" id="preview-INDEX" style="height: 150px; background-color: #eee; display: flex; align-items: center; justify-content: center; cursor: pointer; background-size: cover; background-position: center;" onclick="openMediaModal('input-INDEX', 'preview-INDEX')">
                        <span>Select Image</span>
                    </div>
                    <input type="hidden" name="content[blocks][INDEX][data][image]" id="input-INDEX" onchange="updateBlockPreview('INDEX', 'text_provocation_image', this.value)">
                </div>
            </div>
        </div>
    </div>
</template>
<template id="tpl-custom_html">
    <div class="block-item" data-type="custom_html"><input type="hidden" name="content[blocks][INDEX][type]" value="custom_html">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-code"></i> CUSTOM HTML/SCRIPT</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">HTML/JavaScript Code</label>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">⚠️ Paste your HTML/JS code here. It will be rendered as-is in production.</p>
                <textarea class="form-control" name="content[blocks][INDEX][data][html]" rows="12" style="font-family: 'Courier New', monospace; font-size: 0.9rem;" placeholder="<section>&#10;  <div id='my-embed'></div>&#10;  <script>&#10;    // Your code here&#10;  </script>&#10;</section>" oninput="updateBlockPreview('INDEX', 'custom_html', this.value)"></textarea>
            </div>
        </div>
    </div>
</template>
<template id="tpl-text_large">
    <div class="block-item" data-type="text_large"><input type="hidden" name="content[blocks][INDEX][type]" value="text_large">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-bars"></i> TEXTO LARGO</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Subtítulo</label>
                <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('h2-INDEX', 'a')"><i class="fas fa-link"></i></button></div>
                <div id="h2-INDEX-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('INDEX', 'h2', this.innerHTML)"></div>
                <input type="hidden" id="h2-INDEX" name="content[blocks][INDEX][data][h2]">
            </div>
            <div class="rt-toolbar">
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('textarea-INDEX', 'b')"><b>B</b></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('textarea-INDEX', 'a')"><i class="fas fa-link"></i></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('textarea-INDEX', 'span', 'span-resaltado')"><b>H</b></button>
            </div>
            <div id="textarea-INDEX-editor" class="rich-editor" contenteditable="true" oninput="updateBlockPreview('INDEX', 'content', this.innerHTML)"></div>
            <input type="hidden" id="textarea-INDEX" name="content[blocks][INDEX][data][content]">
        </div>
    </div>
</template>
    <div class="block-item" data-type="gallery_rail"><input type="hidden" name="content[blocks][INDEX][type]" value="gallery_rail">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-bars"></i> SCROLL STRIP (PARALLAX)</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Frase Destacada (Scroll Strip)</label>
                <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-INDEX', 'a')"><i class="fas fa-link"></i></button></div>
                <div id="textarea-INDEX-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateGalleryPreview('INDEX'); document.getElementById('textarea-INDEX').value = this.innerHTML;"></div>
                <input type="hidden" id="textarea-INDEX" name="content[blocks][INDEX][data][phrase]">
            </div>
            <div class="gallery-items-container" id="gallery-items-INDEX" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap:10px;"></div>
            <button type="button" class="form-control" style="margin-top:10px;" onclick="addGalleryItem('INDEX')">+ Añadir Imagen</button>
        </div>
    </div>
</template>
<template id="tpl-gallery-item">
    <div class="gallery-item" style="position:relative; width:100%; aspect-ratio:1; border:1px solid #ddd; border-radius:8px; overflow:hidden; background:#eee;">
        <button type="button" class="btn-remove-gam" style="position:absolute; top:2px; right:2px; background:red; color:white; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; z-index:10;" onclick="removeGalleryItem(this, 'BLOCK_INDEX')">x</button>
        <div class="media-preview-box" id="preview-BLOCK_INDEX-ITEM_INDEX" style="width:100%; height:100%; background-size:cover; background-position:center; cursor:pointer;" onclick="openMediaModal('input-BLOCK_INDEX-ITEM_INDEX', 'preview-BLOCK_INDEX-ITEM_INDEX')"></div>
        <input type="hidden" name="content[blocks][BLOCK_INDEX][data][gallery_items][ITEM_INDEX][image]" id="input-BLOCK_INDEX-ITEM_INDEX" onchange="updateGalleryPreview('BLOCK_INDEX')">
        <input type="file" name="content[blocks][BLOCK_INDEX][data][gallery_items][ITEM_INDEX][image_file]" style="display:none">
    </div>
</template>
<template id="tpl-carousel_adv">
    <div class="block-item" data-type="carousel_adv"><input type="hidden" name="content[blocks][INDEX][type]" value="carousel_adv">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-bars"></i> CAROUSEL GRID (35/65)</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body"><div class="slides-container" id="slides-INDEX"></div><button type="button" class="form-control" onclick="addCarouselSlide('INDEX')">+ Añadir Slide</button></div>
    </div>
</template>
<template id="tpl-slide-item">
    <div class="slide-item" style="border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-bottom: 15px; position:relative;">
        <button type="button" class="block-btn remove" style="position:absolute; top:5px; right:5px; background:#f44336; color:white; border:none; width:20px; height:20px; border-radius:50%; cursor:pointer;" onclick="removeCarouselSlide(this, 'BLOCK_INDEX')">x</button>
        
        <div class="form-group">
            <label class="form-label">Título</label>
            <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('slide-title-BLOCK_INDEX-SLIDE_INDEX', 'a')"><i class="fas fa-link"></i></button></div>
            <div id="slide-title-BLOCK_INDEX-SLIDE_INDEX-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateCarouselPreview('BLOCK_INDEX'); document.getElementById('slide-title-BLOCK_INDEX-SLIDE_INDEX').value = this.innerHTML;"></div>
            <input type="hidden" id="slide-title-BLOCK_INDEX-SLIDE_INDEX" name="content[blocks][BLOCK_INDEX][data][slides][SLIDE_INDEX][title]">
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <div class="rt-toolbar">
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('slide-desc-BLOCK_INDEX-SLIDE_INDEX', 'b')"><b>B</b></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('slide-desc-BLOCK_INDEX-SLIDE_INDEX', 'a')"><i class="fas fa-link"></i></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('slide-desc-BLOCK_INDEX-SLIDE_INDEX', 'span', 'span-resaltado span-blanco')"><b>H</b></button>
            </div>
            <div id="slide-desc-BLOCK_INDEX-SLIDE_INDEX-editor" class="rich-editor" contenteditable="true" oninput="updateCarouselPreview('BLOCK_INDEX'); document.getElementById('slide-desc-BLOCK_INDEX-SLIDE_INDEX').value = this.innerHTML;"></div>
            <input type="hidden" id="slide-desc-BLOCK_INDEX-SLIDE_INDEX" name="content[blocks][BLOCK_INDEX][data][slides][SLIDE_INDEX][description]">
        </div>

        <div class="media-selector-wrapper" style="margin-top:10px;">
            <div class="media-preview-box" id="preview-BLOCK_INDEX-SLIDE_INDEX" 
                 style="height: 100px; background-size: cover; background-position: center; border-radius: 6px; margin-bottom: 5px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                 onclick="openMediaModal('input-BLOCK_INDEX-SLIDE_INDEX', 'preview-BLOCK_INDEX-SLIDE_INDEX')">
                <span style="font-size:0.7rem; color:#999;">Select Image</span>
            </div>
            <input type="hidden" name="content[blocks][BLOCK_INDEX][data][slides][SLIDE_INDEX][image]" id="input-BLOCK_INDEX-SLIDE_INDEX" value="" onchange="updateCarouselPreview('BLOCK_INDEX')">
            <input type="file" name="content[blocks][BLOCK_INDEX][data][slides][SLIDE_INDEX][image_file]" class="form-control" style="display:none">
            <button type="button" class="form-control" style="padding:5px; font-size:0.8rem;" onclick="openMediaModal('input-BLOCK_INDEX-SLIDE_INDEX', 'preview-BLOCK_INDEX-SLIDE_INDEX')">Select from Library</button>
        </div>
    </div>
</template>

<script>
    window.rootPath = "{{ asset('/') }}";
    let blockIndex = {{ count(old('content.blocks', [])) > 0 ? max(array_keys(old('content.blocks'))) + 1 : 1 }};

    // Shadow DOM Integration
    document.addEventListener('DOMContentLoaded', () => {
        const host = document.getElementById('preview-viewport');
        if(!host.shadowRoot) {
            const shadow = host.attachShadow({mode: 'open'});
            
            // External CSS
            const linkGlobal = document.createElement('link');
            linkGlobal.setAttribute('rel', 'stylesheet');
            linkGlobal.setAttribute('href', '{{ asset('css/style-global-blog.css') }}');
            shadow.appendChild(linkGlobal);

            const linkLinks = document.createElement('link');
            linkLinks.setAttribute('rel', 'stylesheet');
            linkLinks.setAttribute('href', '{{ asset('css/link-styles.css') }}');
            shadow.appendChild(linkLinks);

            const linkBlog = document.createElement('link');
            linkBlog.setAttribute('rel', 'stylesheet');
            linkBlog.setAttribute('href', '{{ asset('css/blog.css') }}');
            shadow.appendChild(linkBlog);


            // Internal Overrides (Force Mobile View @500px)
            const style = document.createElement('style');
            style.textContent = `
                :host { display: block; overflow-y: auto; height: 100%; background: #111; color: #eee; }
                
                .preview-viewport { background: #111; min-height: 100%; }
                
                /* FONT FACES for Shadow DOM */
                @font-face { font-family: "PowerGrotesk"; src: url("{{ asset('fonts/power_grotesk-medium-webfont.woff2') }}") format("woff2"); font-weight: 500; }
                @font-face { font-family: "PowerGrotesk bold"; src: url("{{ asset('fonts/power_grotesk-medium-webfont.woff2') }}") format("woff2"); font-weight: 700; }
                
                /* FORCE MOBILE STYLES (Exact copy from style-global-blog.css @media max-width: 768px & 500px) */
                html, body { padding-left: 0 !important; overflow-x: hidden; background: transparent; }
                
                h1, h2, h3, .Title h1, .Title h2, .Title h3, .card h1, .card h2, .card h3 {
                    font-family: "PowerGrotesk", sans-serif !important;
                }

                .card { 
                    padding: 2rem 1.5rem !important;
                    border-radius: 1.5rem !important;
                    width: 85% !important;
                    max-width: 700px !important;
                    margin: 0 auto 20px auto !important;
                    transform: none !important;
                }
                
                .TextLarge { padding: 2rem 1rem !important; font-size: 0.9rem !important; }
                .TextLarge h2 { font-size: 1.3rem !important; } 
                .TextLarge div { max-width: 95% !important; }
                
                .Title { width: 90% !important; margin: 0 auto 20px auto !important; padding: 40px 0 !important; text-align: center !important; }
                .Title h1 { font-size: 2.2rem !important; margin: 10px 0 !important; line-height: 1.1 !important; } 
                .Title h2 { font-size: 1.2rem !important; margin: 0 !important; font-weight: 400 !important; }
                .Title .blog-category-badge { font-size: 1rem !important; padding: 4px 15px !important; }
                
                .span-resaltado { 
                    font-family: inherit;
                    font-size: 1.15rem; 
                    color: #ff472b; 
                    font-weight: 600; 
                }

                .carousel-item.card-item .span-resaltado.span-blanco {
                    color: #fff !important;
                    font-size: 0.9rem;
                    margin: 15px 0;
                }
                
                /* CAROUSEL FIX: Image Left, Text Right */
                .carousel-item.card-item { 
                    display: grid !important; 
                    grid-template-columns: 40% 1fr !important;
                    gap: 15px !important;
                    align-items: center !important;
                    width: 100% !important; 
                    max-width: 100% !important;
                    margin-bottom: 20px !important;
                    text-align: left !important;
                }
                .carousel-item.card-item img.carrucel-imagen { 
                    width: 100% !important; 
                    height: auto !important; 
                    object-fit: cover !important;
                    border-radius: 8px !important;
                }
                .carousel-item.card-item.no-image {
                    grid-template-columns: 1fr !important;
                }
                .carousel-item.card-item.no-image {
                    grid-template-columns: 1fr !important;
                }
                .carousel-item.card-item h2 { font-size: 1rem !important; margin: 0 0 5px 0 !important; }
                .carousel-item.card-item p { font-size: 0.8rem !important; margin: 0 !important; }

                /* Scroll Strip Mobile Simulation (Vertical Scatter Fidelity) */
                .blog-scroll-strip__rail {
                    display: flex !important;
                    flex-wrap: wrap !important; /* Allow wrapping for scatter effect */
                    justify-content: center !important;
                    align-items: flex-start !important;
                    gap: 15px !important;
                    padding: 20px 10px !important;
                    background: transparent !important;
                    overflow: visible !important; /* Let it grow vertically */
                }
                .blog-scroll-card {
                    width: 140px !important; 
                    max-width: 45% !important; /* Ensure 2 columns on small preview */
                    border-radius: 10px !important;
                    flex-shrink: 0 !important;
                    margin: 0 !important;
                    position: relative !important;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
                    overflow: hidden !important;
                    transition: transform 0.3s ease !important;
                }
                
                /* Pseudo-Scatter Effect */
                .blog-scroll-card:nth-child(odd) { transform: translateY(20px) !important; }
                .blog-scroll-card:nth-child(even) { transform: translateY(-10px) !important; }
                .blog-scroll-card:hover { z-index: 10 !important; transform: scale(1.05) !important; }
                .blog-scroll-card img {
                    width: 100% !important;
                    height: auto !important;
                    border-radius: 10px !important;
                    display: block !important;
                }
                
                /* Phrase Section Simulation */
                .blog-scroll-strip__phrase-container {
                    background-color: #eeeeee !important; 
                    color: #1a1a1a !important; 
                    padding: 40px 20px !important; /* More breathing room */
                    text-align: center !important;
                    margin: 30px 0 !important;
                    border-radius: 8px !important;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
                }
                .blog-scroll-strip__phrase {
                    max-width: 100% !important;
                }
                .blog-scroll-strip__phrase h2 {
                    font-family: 'PowerGrotesk', sans-serif !important;
                    font-size: 1.2rem !important;
                    color: #1a1a1a !important; /* Ensure dark text on light bg */
                    margin: 0 !important;
                }

                .horizontal-scroll-section { position: relative !important; height: auto !important; background: transparent !important; padding: 0 !important; justify-content: flex-start !important; overflow-x: auto !important; }
                
                /* Provocation Block - Mobile View (500px) */
                .text-provocation {
                    display: grid !important;
                    grid-template-columns: 1fr !important;
                    gap: 1.5rem !important;
                    padding: 2rem 1rem !important;
                    background-color: #1a1a1a !important;
                    text-align: center !important;
                    border-radius: 8px !important;
                    margin: 20px auto !important;
                    width: 90% !important;
                }
                .text-provocation img {
                    width: 100% !important;
                    height: auto !important;
                    object-fit: contain !important;
                    border-radius: 8px !important;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5) !important;
                }
                .text-provocation .provoc-text {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .text-provocation h2 {
                    font-family: "PowerGrotesk", sans-serif !important;
                    font-size: 1.5rem !important;
                    line-height: 1.1 !important;
                    color: #eee !important;
                    margin: 0 !important;
                    text-align: center !important;
                    font-weight: 400 !important;
                }
                
                /* Specific Intro Glass Style (if different, but usually it's just .card) */
                /* If user meant rounded corners like card-img, we might need a mockup class, but standard .card is 1.5rem radius */
            `;
            shadow.appendChild(style);

            // Content
            const tpl = document.getElementById('initial-preview-html');
            const wrapper = document.createElement('div');
            wrapper.className = "preview-wrapper";
            wrapper.id = "preview-blocks-list";
            wrapper.appendChild(tpl.content.cloneNode(true));
            shadow.appendChild(wrapper);

            window.previewShadow = shadow; // Expose for updates
        }

        // Card Preview Shadow DOM
        const cardHost = document.getElementById('card-preview-host');
        if(cardHost && !cardHost.shadowRoot) {
            const cardShadow = cardHost.attachShadow({mode: 'open'});
             // External CSS
            const linkGlobal = document.createElement('link');
            linkGlobal.setAttribute('rel', 'stylesheet');
            linkGlobal.setAttribute('href', '{{ asset('css/style-global-blog.css') }}');
            cardShadow.appendChild(linkGlobal);

            const linkBlog = document.createElement('link');
            linkBlog.setAttribute('rel', 'stylesheet');
            linkBlog.setAttribute('href', '{{ asset('css/blog.css') }}');
            cardShadow.appendChild(linkBlog);
            
            // Internal Overrides
            const style = document.createElement('style');
            style.textContent = `
                :host { display: block; --main-font: "PowerGrotesk", sans-serif; --bold-font: "PowerGrotesk bold", sans-serif; }
                a { text-decoration: none; color: inherit; }
                .blog-item1 { margin: 0; width: 100%; border:none; background: transparent; }
                .blog-thumb { width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; border-radius: 8px; }
                .blog-info1 { padding: 15px 0; color: #eee; }
                .blog-title1 { color: #eee !important; margin: 10px 0; font-family: var(--bold-font); }
                .blog-category-badge { color: #fff !important; }
                .blog-date1 { color: #888 !important; font-size: 0.8rem; }
            `;
            cardShadow.appendChild(style);

            const tpl = document.getElementById('tpl-card-initial');
            cardShadow.appendChild(tpl.content.cloneNode(true));
            window.cardShadow = cardShadow;
        }
    });

    function updateCardPreview(field, val) {
        if(!window.cardShadow) return;
        
        if(field === 'title') {
            window.cardShadow.getElementById('card-title-preview').innerText = val || 'Título del Proyecto';
             updatePreview('global', 'title', val);
        }
        if(field === 'category') {
            const el = window.cardShadow.getElementById('card-category-preview');
            el.innerText = val;
            el.style.display = val ? 'inline-block' : 'none';
            
            // Sync logic to Hero H3
            const heroH3Input = document.getElementById('hero-h3');
            const heroH3Editor = document.getElementById('hero-h3-editor');
            if(heroH3Input && heroH3Editor) { 
                heroH3Input.value = val; 
                heroH3Editor.innerHTML = val;
                // Lock if category is selected
                if (val) {
                    heroH3Editor.contentEditable = "false";
                    heroH3Editor.style.opacity = "0.7";
                    heroH3Editor.style.cursor = "not-allowed";
                } else {
                    heroH3Editor.contentEditable = "true";
                    heroH3Editor.style.opacity = "1";
                    heroH3Editor.style.cursor = "text";
                }
                updateBlockPreview('0', 'h3', val); 
            }
        }
        if(field === 'badge_color') {
            const el = window.cardShadow.getElementById('card-category-preview');
            if(el) el.className = 'blog-category-badge ' + val;
            
             // SYNC TO HERO H3 COLOR in Preview
            if(window.previewShadow) {
                const heroP = window.previewShadow.getElementById('prev-block-0');
                if(heroP) {
                    const h3 = heroP.querySelector('.h3');
                    if(h3) {
                        // Remove old cat-grad classes
                        h3.classList.remove('cat-grad-1', 'cat-grad-2', 'cat-grad-3', 'cat-grad-4');
                        h3.classList.add(val);
                    }
                }
            }
        }
        if(field === 'date' || field === 'coming_soon') {
            const isComingSoon = document.getElementById('check-coming-soon').checked;
            const dateVal = document.getElementById('input-date').value;
             const elDate = window.cardShadow.getElementById('card-date-preview');
            if(isComingSoon) { elDate.innerText = 'PRÓXIMAMENTE'; return; }
            if(!dateVal) { elDate.innerText = 'Draft'; return; }
            const date = new Date(dateVal);
            const options = { year: 'numeric', month: 'long' };
            elDate.innerText = date.toLocaleDateString('en-US', options);
        }
        if(field === 'image') {
            if(val) {
                const baseUrl = window.rootPath || '/';
                window.cardShadow.getElementById('card-img-preview').src = baseUrl + 'storage/' + val;
            }
        }
    }

    // Obsolete setTheme function removed as UI is gone.

    function addBlock(type) {
        if(type === 'intro_glass' && isIntroGlassDisabled()) return;

        const wrapper = document.getElementById('blocks-wrapper');
        const template = document.getElementById('tpl-' + type);
        wrapper.insertAdjacentHTML('beforeend', template.innerHTML.replace(/INDEX/g, blockIndex));
        
        // Shadow DOM Preview
        if(window.previewShadow) {
            const list = window.previewShadow.getElementById('preview-blocks-list');
            const newItem = document.createElement('div');
            newItem.id = 'prev-block-' + blockIndex;
            newItem.className = 'preview-item';
            newItem.dataset.type = type;
            // Initialize mock content structure matches Global CSS expectations
            if(type === 'intro_glass') newItem.innerHTML = '<div class="card" style="padding: 2rem;"></div>';

            else if(type === 'text_large') newItem.innerHTML = '<div class="TextLarge" style="padding: 20px;"><h2></h2><div></div></div>';
            else if(type === 'statement') newItem.innerHTML = '<div class="statement" style="padding: 20px; text-align: center; font-size: 1.5rem; font-weight: bold;"></div>';
            else if(type === 'text_provocation') newItem.innerHTML = '<div class="text-provocation" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 2rem 1rem; background: #1a1a1a;"><img src="" style="width: 100%; display: none;"><div class="provoc-text"><h2 style="color: #eee;"></h2></div></div>';
            else if(type === 'custom_html') newItem.innerHTML = '<div class="custom-html-preview" style="padding: 20px; background: #f5f5f5; border: 2px dashed #999; text-align: center; color: #666; font-family: monospace;">Custom HTML Block (Preview Disabled)</div>';
            // Gallery/Carousel initialized empty
            else if(type === 'gallery_rail') newItem.innerHTML = '<div class="blog-scroll-strip"><div class="blog-scroll-strip__inner"><div class="blog-scroll-strip__rail"></div></div></div>';
            else if(type === 'carousel_adv') newItem.innerHTML = '<div class="carousel-wrapper-preview" style="display:flex; flex-direction:column; gap:15px;"></div>';
            
            list.appendChild(newItem);
        }

        // Initialize Rich Editors for this block
        if(type === 'intro_glass') {
            initRichEditor(`textarea-${blockIndex}-editor`, `textarea-${blockIndex}`);

        } else if(type === 'statement') {
            const temp = document.getElementById('tpl-statement');
            html = temp.innerHTML.replace(/INDEX/g, count);
        } else if(type === 'text_provocation') {
            const temp = document.getElementById('tpl-text_provocation');
            html = temp.innerHTML.replace(/INDEX/g, count);

        } else if(type === 'gallery_rail') {
            initRichEditor(`textarea-${blockIndex}-editor`, `textarea-${blockIndex}`);
        } else if(type === 'text_large') {
            initRichEditor(`h2-${blockIndex}-editor`, `h2-${blockIndex}`);
            initRichEditor(`textarea-${blockIndex}-editor`, `textarea-${blockIndex}`);
        }

        blockIndex++;
        checkIntroGlassConstraint();
        initSortable();
    }

    function removeBlock(btn, e) {
        e.stopPropagation();
        if(confirm('¿Eliminar bloque?')) {
            const item = btn.closest('.block-item');
            const idxRegex = item.querySelector('input[name*="[type]"]').name.match(/\[(\d+)\]/);
            if(idxRegex) {
                const idx = idxRegex[1];
                item.remove();
                if(window.previewShadow) {
                    const p = window.previewShadow.getElementById('prev-block-' + idx);
                    if(p) p.remove();
                }
            }
            setTimeout(checkIntroGlassConstraint, 50);
        }
    }

    function toggleBlock(header) { if(!event.target.closest('.block-btn')) header.nextElementSibling.classList.toggle('collapsed'); }

    function addCarouselSlide(blockIdx) {
        const container = document.getElementById('slides-' + blockIdx);
        const count = container.querySelectorAll('.slide-item').length;
        container.insertAdjacentHTML('beforeend', document.getElementById('tpl-slide-item').innerHTML.replace(/BLOCK_INDEX/g, blockIdx).replace(/SLIDE_INDEX/g, count));
        
        initRichEditor(`slide-title-${blockIdx}-${count}-editor`, `slide-title-${blockIdx}-${count}`);
        initRichEditor(`slide-desc-${blockIdx}-${count}-editor`, `slide-desc-${blockIdx}-${count}`);

        updateCarouselPreview(blockIdx);
    }

    function formatText(id, tag, className = '') {
        const editor = document.getElementById(id + '-editor');
        if (!editor) return;
        
        editor.focus();
        if (tag === 'b') {
            document.execCommand('bold', false, null);
        } else if (tag === 'span') {
            const selection = window.getSelection();
            if (!selection.rangeCount || selection.isCollapsed) return;
            const range = selection.getRangeAt(0);
            
            // Check if we are already inside the target span
            let container = range.commonAncestorContainer;
            if (container.nodeType === 3) container = container.parentNode;
            
            // Find closest span with this class
            let existingSpan = container.closest('span.' + className.split(' ').join('.'));
            
            if (existingSpan) {
                // Unwrap
                const parent = existingSpan.parentNode;
                while (existingSpan.firstChild) {
                    parent.insertBefore(existingSpan.firstChild, existingSpan);
                }
                parent.removeChild(existingSpan);
            } else {
                // Wrap
                // Check if selection contains other spans and strip them if they conflict? 
                // For simplicity, just wrap. If user selects multiple times, we rely on unwrapping next time.
                // Better approach: Clean range first?
                // Let's stick to basic wrapping but ensure we don't nest identical spans if possible.
                
                const span = document.createElement('span');
                span.className = className;
                try {
                    range.surroundContents(span);
                } catch (e) {
                    // Fallback for complex selections (crossing boundaries)
                    document.execCommand('insertHTML', false, `<span class="${className}">${selection.toString()}</span>`);
                }
            }
            // Trigger input for sync
            editor.dispatchEvent(new Event('input', { bubbles: true }));
        } else if (tag === 'a') {
            let currentUrl = "https://";
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const container = selection.getRangeAt(0).commonAncestorContainer;
                const link = container.nodeName === 'A' ? container : container.parentNode;
                if (link && link.nodeName === 'A') {
                    currentUrl = link.getAttribute('href');
                }
            }

            const url = prompt("Enter URL (clear to remove):", currentUrl);
            if (url !== null) {
                if (url === "") {
                    document.execCommand('unlink', false, null);
                } else {
                    document.execCommand('createLink', false, url);
                    // Selection might have changed, re-fetch to set target
                    const newSelection = window.getSelection();
                    if (newSelection.rangeCount > 0) {
                        const newContainer = newSelection.getRangeAt(0).commonAncestorContainer;
                        const newLink = newContainer.nodeName === 'A' ? newContainer : newContainer.parentNode;
                        if (newLink && newLink.nodeName === 'A') {
                            newLink.target = "_blank";
                        }
                    }
                }
            }
        }
    }

    // NEW: Handle Rich Editor Content Sync
    function initRichEditor(id, syncId) {
        const editor = document.getElementById(id);
        const textarea = document.getElementById(syncId);
        if (!editor || !textarea) return;

        // Load initial content
        editor.innerHTML = textarea.value;

        editor.addEventListener('input', () => {
            textarea.value = editor.innerHTML;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // Prevent bold in specific fields if needed
        editor.addEventListener('keydown', (e) => {
            if (editor.classList.contains('no-bold') && (e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
            }
        });

        // Clean paste
        editor.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    }

    function updatePreview(scope, field, val) { if(scope === 'global' && field === 'title') updateBlockPreview('0', 'h1', val); }

    function updateBlockPreview(idx, field, val) {
        if(!window.previewShadow) return; 
        const p = window.previewShadow.getElementById('prev-block-' + idx);
        if(!p) return;
        const type = p.dataset.type;

        if(type === 'hero') {
            if(field === 'h3') p.querySelector('.h3').innerText = val;
            if(field === 'h1') p.querySelector('h1').innerText = val;
            if(field === 'h2') p.querySelector('h2').innerText = val;
        }
        else if(type === 'intro_glass') {
            let el = p.querySelector('.card');
            if(!el) { p.innerHTML = '<div class="card" style="padding: 2rem;"></div>'; el = p.querySelector('.card'); }
            el.innerHTML = val;
        }
        else if(type === 'phrase') {
            let el = p.querySelector('.span-resaltado');
            if(!el) { p.innerHTML = '<div class="span-resaltado" style="margin: 20px 0;"></div>'; el = p.querySelector('.span-resaltado'); }
            el.innerText = val;
        }
        else if(type === 'statement') {
            let el = p.querySelector('.statement h2');
            if(!el) { 
                p.innerHTML = '<div class="statement"><div class="content-text"><h2></h2></div></div>'; 
                el = p.querySelector('h2'); 
            }
            el.innerText = val;
        }
        else if(type === 'text_provocation') {
             const container = p.querySelector('.text-provocation');
             if(!container) { 
                 p.innerHTML = '<div class="text-provocation" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 2rem; background: #1a1a1a;"><img src="" style="width: 100%; display: none;"><div class="provoc-text"><h2 style="color: #eee;"></h2></div></div>'; 
             }
             // Handle text update
             if(field === 'text_provocation' || field === undefined) {
                 const h2 = p.querySelector('.provoc-text h2');
                 if(h2) h2.innerText = val;
             }
             // Handle image update
             if(field === 'text_provocation_image') {
                 const img = p.querySelector('.text-provocation img');
                 if(img && val) {
                     img.src = `${window.rootPath}storage/${val}`;
                     img.style.display = 'block';
                 }
              }
         }
        else if(type === 'custom_html') {
             // For security, we don't render custom HTML in preview
             // Just show a placeholder
             const container = p.querySelector('.custom-html-preview');
             if(!container) {
                 p.innerHTML = '<div class="custom-html-preview" style="padding: 20px; background: #f5f5f5; border: 2px dashed #999; text-align: center; color: #666; font-family: monospace;">Custom HTML Block<br><small>Preview disabled for security</small></div>';
             }
        }
        else if(type === 'text_large') {
            let container = p.querySelector('.TextLarge');
            if(!container) { p.innerHTML = '<div class="TextLarge" style="padding: 20px;"><h2></h2><div></div></div>'; container = p.querySelector('.TextLarge'); }
            if(field === 'h2') container.querySelector('h2').innerText = val;
            if(field === 'content') container.querySelector('div').innerHTML = val;
        }
    }

    function updateCarouselPreview(idx) {
        if(!window.previewShadow) return;
        const pBlock = window.previewShadow.getElementById('prev-block-' + idx);
        if(!pBlock) return;
        const slides = document.getElementById('slides-' + idx).querySelectorAll('.slide-item');
        let html = '<div class="carousel-wrapper-preview" style="display:flex; flex-direction:column; gap:15px;">';
        const baseUrl = window.rootPath || '/';
        slides.forEach((s, sIdx) => {
            const titleInput = s.querySelector(`input[id^="slide-title-"]`);
            const descInput = s.querySelector(`input[id^="slide-desc-"]`);
            const title = titleInput ? titleInput.value : "Slide";
            const desc = descInput ? descInput.value : "";
            
            const hiddenInput = s.querySelector('input[type="hidden"][name*="image"]');
            const imgSrc = (hiddenInput && hiddenInput.value) ? `${baseUrl}storage/${hiddenInput.value}` : '';
            
            const noImgClass = imgSrc ? '' : 'no-image';
            const imgTag = imgSrc ? `<img src="${imgSrc}" class="carrucel-imagen">` : '';

            html += `<div class="carousel-item card-item ${noImgClass}" id="p-slide-${idx}-${sIdx}">${imgTag}<div><h2>${title}</h2><p>${desc}</p></div></div>`;
        });
        pBlock.innerHTML = html + '</div>';
    }

        function removeCarouselSlide(btn, blockIdx) {
            if(confirm('Eliminar slide?')) {
                btn.closest('.slide-item').remove();
                updateCarouselPreview(blockIdx);
            }
        }



    function updateHeroBg(val) {
        if(!val || !window.previewShadow) return;
        const baseUrl = window.rootPath || '/';
        const url = `${baseUrl}storage/${val}`;
        const el = window.previewShadow.getElementById('hero-preview-bg');
        if(el) el.style.backgroundImage = `url(${url})`;
    }

    function addGalleryItem(blockIdx) {
        const container = document.getElementById('gallery-items-' + blockIdx);
        const count = container.querySelectorAll('.gallery-item').length;
        container.insertAdjacentHTML('beforeend', document.getElementById('tpl-gallery-item').innerHTML.replace(/BLOCK_INDEX/g, blockIdx).replace(/ITEM_INDEX/g, count));
        updateGalleryPreview(blockIdx);
    }

    function removeGalleryItem(btn, blockIdx) {
        btn.closest('.gallery-item').remove();
        updateGalleryPreview(blockIdx);
    }

    function updateGalleryPreview(idx) {
        if(!window.previewShadow) return;
        const p = window.previewShadow.getElementById('prev-block-' + idx);
        
        // Split logic visualization in preview?
        // For simplicity, we just dump them all in a rail for now.
        // Or we could try to emulate the split.
        
        const phrase = document.getElementById(`textarea-${idx}`)?.value || '';
        const items = document.getElementById('gallery-items-' + idx).querySelectorAll('.gallery-item');
        const root = window.rootPath || '/';
        
        // Build array of images
        let images = [];
        items.forEach(item => {
             const input = item.querySelector('input[type="hidden"]');
             if(input && input.value) images.push(root + 'storage/' + input.value);
        });

        const count = images.length;
        const doSplit = phrase && count >= 4;
        const imgs1 = doSplit ? images.slice(0, Math.ceil(count/2)) : images;
        const imgs2 = doSplit ? images.slice(Math.ceil(count/2)) : [];

        let html = '';
        
        // Run 1
        html += `<div class="blog-scroll-strip"><div class="blog-scroll-strip__inner"><div class="blog-scroll-strip__rail">`;
        imgs1.forEach(src => html += `<figure class="blog-scroll-card"><img src="${src}"></figure>`);
        html += `</div></div></div>`;

        // Phrase
        if(doSplit && phrase) {
            html += `<div class="blog-scroll-strip__phrase-container"><div class="blog-scroll-strip__phrase"><h2>${phrase}</h2></div></div>`;
        }

        // Run 2
        if(doSplit && imgs2.length > 0) {
            html += `<div class="blog-scroll-strip"><div class="blog-scroll-strip__inner"><div class="blog-scroll-strip__rail">`;
            imgs2.forEach(src => html += `<figure class="blog-scroll-card"><img src="${src}"></figure>`);
            html += `</div></div></div>`;
        }

        p.innerHTML = html;
    }

    function initSortable() {
        const el = document.getElementById('blocks-wrapper'); if(!el) return;
        Sortable.create(el, {
            handle: '.block-header', animation: 150,
            onEnd: () => {
                const list = window.previewShadow ? window.previewShadow.getElementById('preview-blocks-list') : null;
                const hero = window.previewShadow ? window.previewShadow.getElementById('prev-block-0') : null;
                if(!list || !hero) return;
                
                // Reorder Preview Elements based on Form Elements
                const fragments = document.createDocumentFragment();
                fragments.appendChild(hero); // Keep Hero top
                
                document.querySelectorAll('#blocks-wrapper .block-item').forEach(item => {
                    const idxInput = item.querySelector('input[name*="[type]"]');
                    if(idxInput) {
                        const idxMatch = idxInput.name.match(/\[(\d+)\]/);
                        if(idxMatch) {
                            const idx = idxMatch[1];
                            const p = window.previewShadow.getElementById('prev-block-' + idx);
                            if(p) fragments.appendChild(p);
                        }
                    }
                });
                list.innerHTML = '';
                list.appendChild(fragments);
                checkIntroGlassConstraint();
            }
        });
    }

    function isIntroGlassDisabled() {
        // Rule: Can only add Intro Glass if NO other block types exist (besides intro_glass itself).
        // If there is ANY block that is NOT 'intro_glass', then it is disabled.
        const blocks = document.querySelectorAll('#blocks-wrapper .block-item');
        for(let i=0; i<blocks.length; i++) {
            if(blocks[i].dataset.type !== 'intro_glass') return true;
        }
        return false;
    }

    function checkIntroGlassConstraint() {
        const btn = document.getElementById('btn-add-intro_glass');
        if(!btn) return;
        
        if(isIntroGlassDisabled()) {
            btn.style.opacity = '0.3';
            btn.style.pointerEvents = 'none';
            btn.style.filter = 'grayscale(1)';
        } else {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            btn.style.filter = 'none';
        }
    }
    function initializeAllRichEditors() {
        // Init Hero Editors
        initRichEditor('hero-h3-editor', 'hero-h3');
        initRichEditor('hero-h1-editor', 'hero-h1');
        initRichEditor('hero-h2-editor', 'hero-h2');

        // Init Block Editors
        document.querySelectorAll('.rich-editor').forEach(editor => {
            if (editor.id.startsWith('hero-')) return; // Already done
            const syncId = editor.id.replace('-editor', '');
            initRichEditor(editor.id, syncId);
        });
    }

    document.addEventListener('DOMContentLoaded', () => { 
        initSortable(); 
        checkIntroGlassConstraint(); 
        initializeAllRichEditors();

        // Initial state sync for Category
        const catSelect = document.querySelector('select[name="category"]');
        if (catSelect) {
            updateCardPreview('category', catSelect.value);
        }
        const badgeColor = document.querySelector('input[name="badge_color"]:checked');
        if (badgeColor) {
            updateCardPreview('badge_color', badgeColor.value);
        }

        // SAVE BUTTON (AJAX)
        const btnSave = document.getElementById('btn-save-draft');
        const form = document.getElementById('projectForm');

        btnSave.addEventListener('click', async (e) => {
            e.preventDefault();
            document.getElementById('form-action').value = 'save';
            
            btnSave.disabled = true;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) {
                    const errText = await response.text();
                    console.error('Server error:', errText);
                    try {
                        const errJson = JSON.parse(errText);
                        alert('Error del servidor: ' + (errJson.message || 'Error de validación'));
                    } catch(e) {
                        alert('Error del servidor (Status ' + response.status + '). Revisa la consola o los logs.');
                    }
                    return;
                }

                const result = await response.json();
                if (result.success) {
                    showToast('Cambios guardados como borrador.');
                    if (result.redirect) {
                        // If it's a new project, redirect to edit page to continue
                        window.location.href = result.redirect;
                    }
                } else {
                    alert('Error al guardar: ' + (result.message || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            } finally {
                btnSave.disabled = false;
                btnSave.innerHTML = 'Guardar Cambios';
            }
        });

        // PUBLISH BUTTON (Standard Redirect)
        const btnPublish = document.getElementById('btn-publish-project');
        btnPublish.addEventListener('click', () => {
            document.getElementById('form-action').value = 'publish';
            form.submit();
        });
    });

    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'cms-toast';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);
        
        // Style toast
        Object.assign(toast.style, {
            position: 'fixed',
            bottom: '30px',
            right: '30px',
            background: '#1a1a1a',
            color: '#fff',
            padding: '12px 24px',
            borderRadius: '8px',
            zIndex: '10000',
            boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
            borderLeft: '4px solid #0f0',
            transition: 'all 0.3s ease',
            opacity: '0',
            transform: 'translateY(20px)'
        });

        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endsection
