@extends('layouts.app')

@section('title', 'Edit Project | INDI Lab')

@section('body-class', 'light-theme')

@section('content')
<!-- External Libs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/components/menu-header.css') }}" />

<!-- Shared CMS Styles -->
<link rel="stylesheet" href="{{ asset('css/cms-editor.css') }}?v={{ time() }}" />
<link rel="stylesheet" href="{{ asset('css/blog.css') }}?v={{ time() }}" />
    <!--  Fix Preview Context overrides 
    /* .preview-viewport styles moved to Shadow DOM injection -->
</style>

<div class="cms-container">
    <div class="editor-main">
        <h1 style="font-weight: 900; font-size: 2.5rem; margin-bottom: 10px; letter-spacing: -0.02em;">Editar Proyecto</h1>
        <p style="color: #888; margin-bottom: 40px;">Gestión profesional con Scroll Strip (Parallax).</p>

        @if ($errors->any())
            <div style="background-color: #fceaea; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('work.update', $project) }}" method="POST" enctype="multipart/form-data" id="projectForm">
            @csrf
            @method('PUT')
            
            <div class="blocks-container">
                <div class="section-header">Configuración Global & Card Preview</div>
                <div style="display: grid; grid-template-columns: 1fr 350px; gap: 40px; align-items: start;">
                    
                    <!-- Left Column: Inputs -->
                    <div class="global-inputs">
                        <div class="form-group">
                            <label class="form-label">Título del Proyecto (Global)</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $project->title) }}" required oninput="updateCardPreview('title', this.value)">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-control" onchange="updateCardPreview('category', this.value)">
                                <option value="">Seleccionar Categoría...</option>
                                <option value="Señales Urbanas" {{ old('category', $project->category) == 'Señales Urbanas' ? 'selected' : '' }}>Señales Urbanas</option>
                                <option value="Sistemas Urbanos" {{ old('category', $project->category) == 'Sistemas Urbanos' ? 'selected' : '' }}>Sistemas Urbanos</option>
                                <option value="Experimentos Urbanos" {{ old('category', $project->category) == 'Experimentos Urbanos' ? 'selected' : '' }}>Experimentos Urbanos</option>
                                <option value="Urban Playbooks" {{ old('category', $project->category) == 'Urban Playbooks' ? 'selected' : '' }}>Urban Playbooks</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Color del Badge</label>
                            <div style="display: flex; gap: 15px; background: #eee; padding: 10px; border-radius: 8px;">
                                @foreach(['cat-grad-1', 'cat-grad-2', 'cat-grad-3', 'cat-grad-4'] as $gClass)
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="badge_color" value="{{ $gClass }}" {{ old('badge_color', $project->badge_color ?? 'cat-grad-1') == $gClass ? 'checked' : '' }} onchange="updateCardPreview('badge_color', this.value)">
                                        <span class="{{ $gClass }}" style="display:inline-block; width:24px; height:24px; border-radius:50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ccc;"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Palabras Clave SEO (Meta Keywords)</label>
                            <input type="text" name="meta_keywords" class="form-control" placeholder="Ej: urbanismo, señales, interactivo" value="{{ old('meta_keywords', $project->meta_keywords) }}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Título Menú (Sticky)</label>
                                <input type="text" name="sticky_title" class="form-control" placeholder="Aparece al hacer scroll" value="{{ old('sticky_title', $project->sticky_title) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="published_at" id="input-date" class="form-control" value="{{ old('published_at', $project->published_at ? $project->published_at->format('Y-m-d') : '') }}" onchange="updateCardPreview('date', this.value)">
                                <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="coming_soon" id="check-coming-soon" value="1" {{ $project->coming_soon ? 'checked' : '' }} onchange="updateCardPreview('coming_soon', this.checked)">
                                    <label for="check-coming-soon" style="font-size: 0.9rem; margin:0; cursor: pointer;">Publicar como "Próximamente"</label>
                                </div>
                            </div>
                        </div>

                        <!-- Card Image Selector -->
                        <div class="form-group">
                            <label class="form-label">Imagen de Portada (Card)</label>
                            <div class="media-selector-wrapper">
                                <div class="media-preview-box" id="preview-cover" 
                                     style="background-image: url('{{ $project->image_path ? asset('storage/'.$project->image_path) : '' }}'); height: 100px; background-size: cover; background-position: center; border-radius: 6px; margin-bottom: 5px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                                     onclick="openMediaModal('input-cover', 'preview-cover')">
                                    @if(!$project->image_path) <span style="font-size:0.8rem; color:#999;">Select Cover Image</span> @endif
                                </div>
                                <input type="hidden" name="image_path" id="input-cover" value="{{ $project->image_path ?? '' }}" onchange="updateCardPreview('image', this.value)">
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
                                    <img src="{{ $project->image_path ? asset('storage/'.$project->image_path) : asset('img/1x/Mesa de trabajo 2.png') }}" class="blog-thumb" id="card-img-preview">
                                    <div class="blog-info1">
                                        <span class="blog-category-badge {{ $project->badge_color ?? 'cat-grad-1' }}" id="card-category-preview" style="display:{{ $project->category ? 'inline-block' : 'none' }};">{{ $project->category }}</span>
                                        <h3 class="blog-title1" id="card-title-preview">{{ $project->title }}</h3>
                                        <p class="blog-date1" id="card-date-preview">{{ $project->coming_soon ? 'PRÓXIMAMENTE' : ($project->published_at ? $project->published_at->format('F Y') : 'Draft') }}</p>
                                    </div>
                                </a>
                            </article>
                        </template>
                    </div>
                </div>
                <!-- Hidden Theme Input -->
                <input type="hidden" name="theme" value="{{ $project->theme ?? 'dark' }}">
            </div>

            @php 
                $blocks = old('content.blocks') ?? ($project->content['blocks'] ?? []);
                if(empty($blocks)) $blocks = [];
                // Ensure array values for JS count
                $blocks = array_values($blocks);

                $heroBlock = null;
                $otherBlocks = [];
                foreach($blocks as $idx => $b) {
                    if($idx == 0) { $heroBlock = $b; }
                    else { $otherBlocks[$idx] = $b; }
                }
                if(!$heroBlock) $heroBlock = ['type'=>'hero', 'data'=>[]];
            @endphp

            <div class="blocks-container" style="border-left: 5px solid rgb(226, 70, 43);">
                <div class="section-header" style="color: rgb(226, 70, 43);">Hero Section (Fijo)</div>
                <input type="hidden" name="content[blocks][0][type]" value="hero">
                <div class="form-group">
                    <label class="form-label">Categoría h3</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h3', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h3-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h3', this.innerHTML)">{!! $heroBlock['data']['h3'] ?? '' !!}</div>
                    <input type="hidden" name="content[blocks][0][data][h3]" id="hero-h3" value="{{ $heroBlock['data']['h3'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Título h1</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h1', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h1-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h1', this.innerHTML)">{!! $heroBlock['data']['h1'] ?? '' !!}</div>
                    <input type="hidden" name="content[blocks][0][data][h1]" id="hero-h1" value="{{ $heroBlock['data']['h1'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tagline h2</label>
                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('hero-h2', 'a')"><i class="fas fa-link"></i></button></div>
                    <div id="hero-h2-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateBlockPreview('0', 'h2', this.innerHTML)">{!! $heroBlock['data']['h2'] ?? '' !!}</div>
                    <input type="hidden" name="content[blocks][0][data][h2]" id="hero-h2" value="{{ $heroBlock['data']['h2'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Hero</label>
                    <select name="hero_type" class="form-control" onchange="toggleHeroType(this.value)">
                        <option value="static" {{ $project->hero_type === 'static' ? 'selected' : '' }}>Imagen Estática (Normal)</option>
                        <option value="sequence" {{ $project->hero_type === 'sequence' ? 'selected' : '' }}>Secuencia de Imágenes (Scroll Parallax)</option>
                    </select>
                </div>

                <div id="hero-static-fields" style="display: {{ $project->hero_type === 'sequence' ? 'none' : 'block' }};">
                    <div class="form-group">
                        <label class="form-label">Imagen de Fondo Hero (Estática)</label>
                        <div class="media-selector-wrapper">
                            <div class="media-preview-box" id="preview-hero" 
                                 style="background-image: url('{{ isset($heroBlock['data']['image']) ? asset('storage/'.$heroBlock['data']['image']) : '' }}'); height: 200px; background-size: cover; background-position: center; border-radius: 8px; margin-bottom: 10px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #eee;" 
                                 onclick="openMediaModal('input-hero', 'preview-hero')">
                                @if(!isset($heroBlock['data']['image'])) <span style="color:#999; font-weight:600;">Click to Open Media Library</span> @endif
                            </div>
                            <input type="hidden" name="content[blocks][0][data][image]" id="input-hero" value="{{ $heroBlock['data']['image'] ?? '' }}" onchange="updateHeroBg(this.value)">
                            <button type="button" class="form-control" onclick="openMediaModal('input-hero', 'preview-hero')"><i class="fas fa-image"></i> Seleccionar Imagen</button>
                        </div>
                    </div>
                </div>

                <div id="hero-sequence-fields" style="display: {{ $project->hero_type === 'sequence' ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label class="form-label">Carpeta de Secuencia (Imágenes para Scroll)</label>
                        <div class="media-selector-wrapper">
                            <div class="media-preview-box" id="preview-hero-folder" 
                                 style="height: 150px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color: #f8f9fa;" 
                                 onclick="openMediaModal('input-hero-folder', 'preview-hero-folder', 'folder')">
                                @if($project->hero_folder_id)
                                    @php
                                        $folderName = $project->heroFolder->name ?? 'ID: ' . $project->hero_folder_id;
                                        $firstHeroImg = $project->heroFolder ? $project->heroFolder->media->sortBy('filename')->first() : null;
                                    @endphp
                                    <div style="text-align:center;">
                                        @if($firstHeroImg)
                                            <img src="{{ asset('storage/'.$firstHeroImg->path) }}" style="width:120px; height:80px; object-fit:cover; border-radius:4px; margin-bottom:10px; border:2px solid #ffc107;">
                                        @else
                                            <i class="fas fa-folder fa-4x" style="color:#ffc107"></i>
                                        @endif
                                        <br><span style="font-weight:700; color:#333;">{{ $folderName }}</span>
                                    </div>
                                @else
                                    <span style="color:#999; font-weight:600;">Click para seleccionar carpeta</span>
                                @endif
                            </div>
                            <input type="hidden" name="hero_folder_id" id="input-hero-folder" value="{{ $project->hero_folder_id ?? '' }}">
                            <button type="button" class="form-control" onclick="openMediaModal('input-hero-folder', 'preview-hero-folder', 'folder')"><i class="fas fa-folder-open"></i> Seleccionar Carpeta</button>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 8px;"><i class="fas fa-info-circle"></i> Sube las imágenes de la secuencia a esta carpeta. Se animarán automáticamente según el orden alfabético.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="blocks-wrapper">
                @foreach($otherBlocks as $idx => $block)
                    @php $type = $block['type']; $data = $block['data'] ?? []; @endphp
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
                                <input type="hidden" id="textarea-{{ $idx }}" name="content[blocks][{{ $idx }}][data][text]" value="{{ $data['text'] ?? '' }}">

                            @elseif($type === 'text_large')
                                <div id="text-large-elements-{{ $idx }}" class="text-large-elements-container">
                                    @php
                                        $elements = $data['elements'] ?? [];
                                        // Migration/Backward Compatibility
                                        if (empty($elements) && (isset($data['h2']) || isset($data['content']))) {
                                            if (!empty($data['h2'])) $elements[] = ['type' => 'h2', 'value' => $data['h2']];
                                            if (!empty($data['content'])) $elements[] = ['type' => 'text', 'value' => $data['content']];
                                        }
                                    @endphp
                                    @foreach($elements as $subIdx => $element)
                                        @if($element['type'] === 'h2')
                                            <div class="sub-element sub-h2" style="border-left: 3px solid #ccc; padding-left: 10px; margin-bottom: 15px; position:relative;">
                                                <button type="button" class="block-btn remove" style="position:absolute; top:0; right:0;" onclick="removeLargeTextElement(this, '{{ $idx }}')">×</button>
                                                <div class="form-group">
                                                    <label class="form-label">Subtítulo</label>
                                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('h2-{{ $idx }}-{{ $subIdx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                                    <div id="h2-{{ $idx }}-{{ $subIdx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateLargeTextPreview('{{ $idx }}')"></div>
                                                    <input type="hidden" name="content[blocks][{{ $idx }}][data][elements][{{ $subIdx }}][type]" value="h2">
                                                    <input type="hidden" id="h2-{{ $idx }}-{{ $subIdx }}" name="content[blocks][{{ $idx }}][data][elements][{{ $subIdx }}][value]" value="{{ $element['value'] ?? '' }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="sub-element sub-text" style="border-left: 3px solid #ccc; padding-left: 10px; margin-bottom: 15px; position:relative;">
                                                <button type="button" class="block-btn remove" style="position:absolute; top:0; right:0;" onclick="removeLargeTextElement(this, '{{ $idx }}')">×</button>
                                                <div class="form-group">
                                                    <label class="form-label">Cuadro de Texto</label>
                                                    <div class="rt-toolbar">
                                                        <button type="button" class="rt-btn" onclick="formatText('para-{{ $idx }}-{{ $subIdx }}', 'b')"><b>B</b></button>
                                                        <button type="button" class="rt-btn" onclick="formatText('para-{{ $idx }}-{{ $subIdx }}', 'a')"><i class="fas fa-link"></i></button>
                                                        <button type="button" class="rt-btn" onclick="formatText('para-{{ $idx }}-{{ $subIdx }}', 'span', 'span-resaltado')"><b>H</b></button>
                                                        <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('para-{{ $idx }}-{{ $subIdx }}', 'ul')"><i class="fas fa-list-ul"></i></button>
                                                    </div>
                                                    <div id="para-{{ $idx }}-{{ $subIdx }}-editor" class="rich-editor" contenteditable="true" oninput="updateLargeTextPreview('{{ $idx }}')"></div>
                                                    <input type="hidden" name="content[blocks][{{ $idx }}][data][elements][{{ $subIdx }}][type]" value="text">
                                                    <input type="hidden" id="para-{{ $idx }}-{{ $subIdx }}" name="content[blocks][{{ $idx }}][data][elements][{{ $subIdx }}][value]" value="{{ $element['value'] ?? '' }}">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="text-large-actions" style="display:flex; gap:10px; margin-top:15px;">
                                    <button type="button" class="form-control" onclick="addLargeTextElement('{{ $idx }}', 'h2')">+ Añadir Subtítulo</button>
                                    <button type="button" class="form-control" onclick="addLargeTextElement('{{ $idx }}', 'text')">+ Añadir Cuadro de Texto</button>
                                </div>
                            @elseif($type === 'statement')
                                <div class="form-group">
                                    <label class="form-label">Texto del Statement</label>
                                    <textarea id="statement-{{ $idx }}" class="form-control" name="content[blocks][{{ $idx }}][data][text]" rows="3" oninput="updateBlockPreview('{{ $idx }}', 'statement', this.value)">{{ $data['text'] ?? '' }}</textarea>
                                </div>
                            @elseif($type === 'text_provocation')
                                <div class="form-group">
                                    <label class="form-label">Texto de Provocación (H2)</label>
                                    <textarea id="prov-text-{{ $idx }}" class="form-control" name="content[blocks][{{ $idx }}][data][text]" rows="2" oninput="updateBlockPreview('{{ $idx }}', 'text_provocation', this.value)">{{ $data['text'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Imagen de Fondo</label>
                                    <div class="media-selector-wrapper">
                                        <div class="media-preview-box" id="preview-{{ $idx }}" 
                                             style="height: 150px; background-color: #eee; display: flex; align-items: center; justify-content: center; cursor: pointer; background-size: cover; background-position: center; @if(isset($data['image'])) background-image: url('{{ asset('storage/'.$data['image']) }}'); @endif" 
                                             onclick="openMediaModal('input-{{ $idx }}', 'preview-{{ $idx }}')">
                                            @if(!isset($data['image'])) <span>Select Image</span> @endif
                                        </div>
                                        <input type="hidden" name="content[blocks][{{ $idx }}][data][image]" id="input-{{ $idx }}" value="{{ $data['image'] ?? '' }}" onchange="updateBlockPreview('{{ $idx }}', 'text_provocation_image', this.value)">
                                    </div>
                                </div>
                            @elseif($type === 'custom_html')
                                <div class="form-group">
                                    <label class="form-label">HTML/JavaScript Code</label>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">⚠️ Paste your HTML/JS code here. It will be rendered as-is in production.</p>
                                    <textarea class="form-control" name="content[blocks][{{ $idx }}][data][html]" rows="12" style="font-family: 'Courier New', monospace; font-size: 0.9rem;" placeholder="<section>&#10;  <div id='my-embed'></div>&#10;  <script>&#10;    // Your code here&#10;  </script>&#10;</section>" oninput="updateBlockPreview('{{ $idx }}', 'custom_html', this.value)">{{ $data['html'] ?? '' }}</textarea>
                                </div>
                            @elseif($type === 'gallery_rail')
                                <div class="form-group">
                                    <label class="form-label">Frase Destacada (Scroll Strip)</label>
                                    <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('textarea-{{ $idx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                    <div id="textarea-{{ $idx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateGalleryPreview('{{ $idx }}'); document.getElementById('textarea-{{ $idx }}').value = this.innerHTML;"></div>
                                    <input type="hidden" id="textarea-{{ $idx }}" name="content[blocks][{{ $idx }}][data][phrase]" value="{{ $data['phrase'] ?? '' }}">
                                </div>
                                <div class="gallery-items-container" id="gallery-items-{{ $idx }}" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap:10px;">
                                    @if(isset($data['images'])) @foreach($data['images'] as $imgIdx => $img)
                                        <div class="gallery-item" style="position:relative; width:100%; aspect-ratio:1; border:1px solid #ddd; border-radius:8px; overflow:hidden; background:#eee;">
                                            <button type="button" class="btn-remove-gam" style="position:absolute; top:2px; right:2px; background:red; color:white; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; z-index:10;" onclick="removeGalleryItem(this, '{{ $idx }}')">x</button>
                                            <div class="media-preview-box" id="preview-{{ $idx }}-g{{ $imgIdx }}" style="width:100%; height:100%; background-size:cover; background-position:center; cursor:pointer; background-image:url('{{ asset('storage/'.$img) }}');" onclick="openMediaModal('input-{{ $idx }}-g{{ $imgIdx }}', 'preview-{{ $idx }}-g{{ $imgIdx }}')"></div>
                                            <input type="hidden" name="content[blocks][{{ $idx }}][data][gallery_items][{{ $imgIdx }}][image]" id="input-{{ $idx }}-g{{ $imgIdx }}" value="{{ $img }}" onchange="updateGalleryPreview('{{ $idx }}')">
                                            <input type="file" name="content[blocks][{{ $idx }}][data][gallery_items][{{ $imgIdx }}][image_file]" style="display:none">
                                        </div>
                                    @endforeach @endif
                                </div>
                                <button type="button" class="form-control" style="margin-top:10px;" onclick="addGalleryItem('{{ $idx }}')">+ Añadir Imagen</button>
                            @elseif($type === 'carousel_adv')
                                <div class="slides-container" id="slides-{{ $idx }}">
                                    @if(isset($data['slides'])) @foreach($data['slides'] as $sIdx => $slide)
                                        <div class="slide-item" style="border:1px solid #eee; padding:15px; border-radius:8px; margin-bottom:15px; position:relative;">
                                            <button type="button" class="block-btn remove" style="position:absolute; top:5px; right:5px; background:#f44336; color:white; border:none; width:20px; height:20px; border-radius:50%; cursor:pointer;" onclick="removeCarouselSlide(this, '{{ $idx }}')">x</button>
                                            <div class="form-group">
                                                <label class="form-label">Título</label>
                                                <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('slide-title-{{ $idx }}-{{ $sIdx }}', 'a')"><i class="fas fa-link"></i></button></div>
                                                <div id="slide-title-{{ $idx }}-{{ $sIdx }}-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateCarouselPreview('{{ $idx }}'); document.getElementById('slide-title-{{ $idx }}-{{ $sIdx }}').value = this.innerHTML;"></div>
                                                <input type="hidden" id="slide-title-{{ $idx }}-{{ $sIdx }}" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][title]" value="{{ $slide['title'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Descripción</label>
                                                <div class="rt-toolbar">
                                                    <button type="button" class="rt-btn" onclick="formatText('slide-desc-{{ $idx }}-{{ $sIdx }}', 'b')"><b>B</b></button>
                                                    <button type="button" class="rt-btn" onclick="formatText('slide-desc-{{ $idx }}-{{ $sIdx }}', 'a')"><i class="fas fa-link"></i></button>
                                                    <button type="button" class="rt-btn" onclick="formatText('slide-desc-{{ $idx }}-{{ $sIdx }}', 'span', 'span-resaltado span-blanco')"><b>H</b></button>
                                                </div>
                                                <div id="slide-desc-{{ $idx }}-{{ $sIdx }}-editor" class="rich-editor" contenteditable="true" oninput="updateCarouselPreview('{{ $idx }}'); document.getElementById('slide-desc-{{ $idx }}-{{ $sIdx }}').value = this.innerHTML;"></div>
                                                <input type="hidden" id="slide-desc-{{ $idx }}-{{ $sIdx }}" name="content[blocks][{{ $idx }}][data][slides][{{ $sIdx }}][description]" value="{{ $slide['description'] ?? '' }}">
                                            </div>
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
                    @php
                        $heroBg = '';
                        if ($project->hero_type === 'sequence' && $project->hero_folder_id) {
                            $firstMedia = $project->heroFolder->media->sortBy('filename')->first();
                            $heroBg = $firstMedia ? asset('storage/'.$firstMedia->path) : '';
                        } else {
                            $heroBg = isset($heroBlock['data']['image']) ? asset('storage/'.$heroBlock['data']['image']) : '';
                        }
                    @endphp
                    <div class="Title" id="hero-preview-bg" style="background-image: url('{{ $heroBg }}'); background-size: cover; background-position: center; border-radius: 12px; margin-bottom: 20px; min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 40px 20px;">
                        <span class="h3 blog-category-badge {{ $project->badge_color ?? 'cat-grad-1' }}" style="margin-bottom: 20px;">{{ $heroBlock['data']['h3'] ?? 'Categoría' }}</span>
                        <h1>{{ $heroBlock['data']['h1'] ?? $project->title }}</h1>
                        <h2>{{ $heroBlock['data']['h2'] ?? '' }}</h2>
                    </div>
                </div>
                @foreach($otherBlocks as $idx => $block)
                    <div class="preview-item" id="prev-block-{{ $idx }}" data-type="{{ $block['type'] }}">
                        @if($block['type'] === 'intro_glass') <div class="card" style="padding: 2rem;">{!! $block['data']['text'] ?? '' !!}</div>
                        @elseif($block['type'] === 'statement') <div class="Statement" style="padding: 20px; text-align: center; font-size: 1.5rem; font-weight: bold;">{{ $block['data']['text'] ?? '' }}</div>
                        @elseif($block['type'] === 'text_provocation') 
                            <div class="text-provocation" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 2rem 1rem; background: #1a1a1a; text-align: center; border-radius: 8px; margin: 20px auto; width: 90%;">
                                @if(isset($block['data']['image'])) <img src="{{ asset('storage/'.$block['data']['image']) }}" style="width: 100%; border-radius: 8px;"> @endif
                                <div class="provoc-text"><h2 style="color: #eee; font-size: 1.5rem; text-align: center;">{{ $block['data']['text'] ?? '' }}</h2></div>
                            </div>
                        @elseif($block['type'] === 'custom_html')
                            <div class="custom-html-preview" style="padding: 20px; background: #f5f5f5; border: 2px dashed #999; text-align: center; color: #666; font-family: monospace; margin: 20px auto; width: 90%;">Custom HTML Block<br><small>Preview disabled for security</small></div>
                        @elseif($block['type'] === 'text_large') <div class="TextLarge" style="padding: 20px;"><h2>{{ $block['data']['h2'] ?? '' }}</h2><div>{!! $block['data']['content'] ?? '' !!}</div></div>
                        @elseif($block['type'] === 'gallery_rail')
                            @php
                                $dImages = $block['data']['images'] ?? [];
                                $dPhrase = $block['data']['phrase'] ?? '';
                                $dCount = count($dImages);
                                $dDoSplit = !empty($dPhrase) && $dCount >= 4;
                                $dImgs1 = $dDoSplit ? array_slice($dImages, 0, ceil($dCount / 2)) : $dImages;
                                $dImgs2 = $dDoSplit ? array_slice($dImages, ceil($dCount / 2)) : [];
                            @endphp
                            <div class="blog-scroll-strip">
                                <div class="blog-scroll-strip__inner">
                                    <div class="blog-scroll-strip__rail">
                                        @foreach($dImgs1 as $img) @if(is_string($img))
                                            <figure class="blog-scroll-card"><img src="{{ asset('storage/'.$img) }}"></figure>
                                        @endif @endforeach
                                    </div>
                                </div>
                            </div>
                            @if($dDoSplit && $dPhrase)
                                <div class="blog-scroll-strip__phrase-container"><div class="blog-scroll-strip__phrase"><h2>{{ $dPhrase }}</h2></div></div>
                            @endif
                            @if($dDoSplit && count($dImgs2) > 0)
                                <div class="blog-scroll-strip">
                                    <div class="blog-scroll-strip__inner">
                                        <div class="blog-scroll-strip__rail">
                                            @foreach($dImgs2 as $img) @if(is_string($img))
                                                <figure class="blog-scroll-card"><img src="{{ asset('storage/'.$img) }}"></figure>
                                            @endif @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($block['type'] === 'carousel_adv')
                            <div>@if(isset($block['data']['slides'])) @foreach(array_slice($block['data']['slides'],0,2) as $s) <div class="carousel-item card-item" style="display:flex; flex-direction:column; gap:10px;"><img src="{{ isset($s['image']) ? asset('storage/'.$s['image']) : '' }}" class="carrucel-imagen" style="width:100%; height:150px; object-fit:cover;"><h2>{{ $s['title'] ?? '' }}</h2><p>{{ $s['description'] ?? '' }}</p></div> @endforeach @endif</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </template>
        
        <div class="publish-area" style="display:flex; gap:10px; flex-direction:column;">
            <button type="button" class="btn-publish" style="background:#ccc; color:#333;" id="btn-save-draft">Guardar Cambios</button>
            <button type="button" class="btn-publish" id="btn-publish-project">Actualizar Proyecto</button>
        </div>
    </aside>
</div>

<!-- TEMPLATES -->

<template id="tpl-intro_glass">
    <div class="block-item" data-type="intro_glass"><input type="hidden" name="content[blocks][INDEX][type]" value="intro_glass">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-certificate"></i> INTRO GLASS</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="rt-toolbar"><button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('textarea-INDEX', 'b')"><b>B</b></button><button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('textarea-INDEX', 'a')"><i class="fas fa-link"></i></button></div>
            <div id="textarea-INDEX-editor" class="rich-editor" contenteditable="true" oninput="updateBlockPreview('INDEX', 'text', this.innerHTML)"></div>
            <input type="hidden" id="textarea-INDEX" name="content[blocks][INDEX][data][text]">
        </div>
    </div>
</template>
<template id="tpl-text_large">
    <div class="block-item" data-type="text_large"><input type="hidden" name="content[blocks][INDEX][type]" value="text_large">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-bars"></i> TEXTO LARGO</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div id="text-large-elements-INDEX" class="text-large-elements-container"></div>
            <div class="text-large-actions" style="display:flex; gap:10px; margin-top:15px;">
                <button type="button" class="form-control" onclick="addLargeTextElement('INDEX', 'h2')">+ Añadir Subtítulo</button>
                <button type="button" class="form-control" onclick="addLargeTextElement('INDEX', 'text')">+ Añadir Cuadro de Texto</button>
            </div>
        </div>
    </div>
</template>

<template id="tpl-text-large-h2">
    <div class="sub-element sub-h2" style="border-left: 3px solid #ccc; padding-left: 10px; margin-bottom: 15px; position:relative;">
        <button type="button" class="block-btn remove" style="position:absolute; top:0; right:0;" onclick="removeLargeTextElement(this, 'BLOCK_INDEX')">×</button>
        <div class="form-group">
            <label class="form-label">Subtítulo</label>
            <div class="rt-toolbar"><button type="button" class="rt-btn" onclick="formatText('h2-BLOCK_INDEX-SUB_INDEX', 'a')"><i class="fas fa-link"></i></button></div>
            <div id="h2-BLOCK_INDEX-SUB_INDEX-editor" class="rich-editor single-line no-bold" contenteditable="true" oninput="updateLargeTextPreview('BLOCK_INDEX')"></div>
            <input type="hidden" name="content[blocks][BLOCK_INDEX][data][elements][SUB_INDEX][type]" value="h2">
            <input type="hidden" id="h2-BLOCK_INDEX-SUB_INDEX" name="content[blocks][BLOCK_INDEX][data][elements][SUB_INDEX][value]">
        </div>
    </div>
</template>

<template id="tpl-text-large-text">
    <div class="sub-element sub-text" style="border-left: 3px solid #ccc; padding-left: 10px; margin-bottom: 15px; position:relative;">
        <button type="button" class="block-btn remove" style="position:absolute; top:0; right:0;" onclick="removeLargeTextElement(this, 'BLOCK_INDEX')">×</button>
        <div class="form-group">
            <label class="form-label">Cuadro de Texto</label>
            <div class="rt-toolbar">
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('para-BLOCK_INDEX-SUB_INDEX', 'b')"><b>B</b></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('para-BLOCK_INDEX-SUB_INDEX', 'a')"><i class="fas fa-link"></i></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('para-BLOCK_INDEX-SUB_INDEX', 'span', 'span-resaltado')"><b>H</b></button>
                <button type="button" class="rt-btn" onmousedown="event.preventDefault(); formatText('para-BLOCK_INDEX-SUB_INDEX', 'ul')"><i class="fas fa-list-ul"></i></button>
            </div>
            <div id="para-BLOCK_INDEX-SUB_INDEX-editor" class="rich-editor" contenteditable="true" oninput="updateLargeTextPreview('BLOCK_INDEX')"></div>
            <input type="hidden" name="content[blocks][BLOCK_INDEX][data][elements][SUB_INDEX][type]" value="text">
            <input type="hidden" id="para-BLOCK_INDEX-SUB_INDEX" name="content[blocks][BLOCK_INDEX][data][elements][SUB_INDEX][value]">
        </div>
    </div>
</template>
<template id="tpl-statement">
    <div class="block-item" data-type="statement"><input type="hidden" name="content[blocks][INDEX][type]" value="statement">
        <div class="block-header" onclick="toggleBlock(this)"><span class="block-title"><i class="fas fa-quote-right"></i> STATEMENT (ANIMATED)</span><button type="button" class="block-btn remove" onclick="removeBlock(this, event)"><i class="fas fa-trash"></i></button></div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Texto del Statement</label>
                <textarea id="statement-INDEX" class="form-control" name="content[blocks][INDEX][data][text]" rows="3" oninput="updateBlockPreview('INDEX', 'statement', this.value)"></textarea>
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
                <textarea id="prov-text-INDEX" class="form-control" name="content[blocks][INDEX][data][text]" rows="2" oninput="updateBlockPreview('INDEX', 'text_provocation', this.value)"></textarea>
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
<template id="tpl-gallery_rail">
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
        <div class="block-body"><div class="slides-container" id="slides-INDEX"></div><button type="button" class="form-control" onclick="addCarouselSlide('INDEX')">+ Slide</button></div>
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
    
    // Shadow DOM Integration
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Sidebar Preview Shadow DOM
        const host = document.getElementById('preview-viewport');
        if(host && !host.shadowRoot) {
            const shadow = host.attachShadow({mode: 'open'});
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
                .TextLarge > div { max-width: 95% !important; }
                
                .Title { width: 90% !important; margin: 0 auto 20px auto !important; padding: 40px 0 !important; text-align: center !important; }
                .Title h1 { font-size: 2.2rem !important; margin: 10px 0 !important; line-height: 1.1 !important; } 
                .Title h2 { font-size: 1.2rem !important; margin: 0 !important; font-weight: 400 !important; }
                .Title .blog-category-badge { font-size: 1rem !important; padding: 4px 15px !important; }
                
                #draft-alert { display: none; } /* Removed old alert bar */
                
                .span-resaltado { font-size: 1rem !important; margin: 20px 0 !important; }
                
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
            `;
            shadow.appendChild(style);
            const tpl = document.getElementById('initial-preview-html');
            const wrapper = document.createElement('div');
            wrapper.className = "preview-wrapper";
            wrapper.id = "preview-blocks-list";
            wrapper.appendChild(tpl.content.cloneNode(true));
            shadow.appendChild(wrapper);
            window.previewShadow = shadow;
        }

        // 2. Card Preview Shadow DOM
        const cardHost = document.getElementById('card-preview-host');
        if(cardHost && !cardHost.shadowRoot) {
            const cardShadow = cardHost.attachShadow({mode: 'open'});
            const linkGlobal = document.createElement('link');
            linkGlobal.setAttribute('rel', 'stylesheet');
            linkGlobal.setAttribute('href', '{{ asset('css/style-global-blog.css') }}');
            cardShadow.appendChild(linkGlobal);

            const linkBlog = document.createElement('link');
            linkBlog.setAttribute('rel', 'stylesheet');
            linkBlog.setAttribute('href', '{{ asset('css/blog.css') }}');
            cardShadow.appendChild(linkBlog);

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
        // Initial state sync
        initializeAllRichEditors();
        syncAllPreviews();

        // 4. Initial state sync for Category
        const catSelect = document.querySelector('select[name="category"]');
        if (catSelect) {
            updateCardPreview('category', catSelect.value);
        }
        const badgeColor = document.querySelector('input[name="badge_color"]:checked');
        if (badgeColor) {
            updateCardPreview('badge_color', badgeColor.value);
        }

        // AJAX SAVE
        const btnSave = document.getElementById('btn-save-draft');
        const form = document.getElementById('projectForm');

        btnSave.addEventListener('click', async (e) => {
            e.preventDefault();
            document.getElementById('form-action').value = 'save';
            
            prepareFormForSubmit();

            btnSave.disabled = true;

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
                    // Show draft alert if it was hidden
                    const alert = document.getElementById('draft-alert');
                    if (alert) alert.style.display = 'flex';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Ocurrió un error al guardar.');
            } finally {
                btnSave.disabled = false;
                btnSave.innerHTML = 'Guardar Cambios';
            }
        });

        // PUBLISH
        const btnPublish = document.getElementById('btn-publish-project');
        btnPublish.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('form-action').value = 'publish';
            prepareFormForSubmit();
            form.submit();
        });
    });

    function prepareFormForSubmit() {
        // Sync rich editors to hidden inputs
        document.querySelectorAll('.rich-editor').forEach(editor => {
            const hiddenInputId = editor.id.replace('-editor', '');
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput) {
                hiddenInput.value = editor.innerHTML;
            }
        });

        // RE-INDEX TEXT LARGE ELEMENTS to ensure DOM order is preserved
        document.querySelectorAll('.text-large-elements-container').forEach(container => {
            const blockIdxMatch = container.id.match(/text-large-elements-(\d+)/);
            if(blockIdxMatch) {
                const blockIdx = blockIdxMatch[1];
                container.querySelectorAll('.sub-element').forEach((el, index) => {
                    // Update all input names within this element
                    el.querySelectorAll('input, select, textarea').forEach(input => {
                        input.name = input.name.replace(/\[elements\]\[\d+\]/, `[elements][${index}]`);
                    });
                });
            }
        });
    }

    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'cms-toast';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);
        Object.assign(toast.style, {
            position: 'fixed', bottom: '30px', right: '30px', background: '#1a1a1a', color: '#fff',
            padding: '12px 24px', borderRadius: '8px', zIndex: '10000', borderLeft: '4px solid #0f0',
            opacity: '0', transform: 'translateY(20px)', transition: 'all 0.3s ease'
        });
        setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 10);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function initializeAllRichEditors() {
        // Init Hero Editors
        initRichEditor('hero-h3-editor', 'hero-h3');
        initRichEditor('hero-h1-editor', 'hero-h1');
        initRichEditor('hero-h2-editor', 'hero-h2');

        // Init Block Editors
        document.querySelectorAll('.rich-editor').forEach(editor => {
            if (editor.id.startsWith('hero-')) return; 
            const syncId = editor.id.replace('-editor', '');
            initRichEditor(editor.id, syncId);
        });
    }

    function syncAllPreviews() {
        // Card Preview Sync
        const titleEl = document.querySelector('input[name="title"]');
        if(titleEl) updateCardPreview('title', titleEl.value);
        
        const catEl = document.querySelector('select[name="category"]');
        if(catEl) updateCardPreview('category', catEl.value);
        
        const badgeEl = document.querySelector('input[name="badge_color"]:checked');
        if(badgeEl) updateCardPreview('badge_color', badgeEl.value);
        
        updateCardPreview('date', document.getElementById('input-date')?.value);
        updateCardPreview('coming_soon', document.getElementById('check-coming-soon')?.checked);
        updateCardPreview('image', document.getElementById('input-cover')?.value);

        // Hero Sync
        updateBlockPreview('0', 'h3', document.getElementById('hero-h3')?.value || '');
        updateBlockPreview('0', 'h1', document.getElementById('hero-h1')?.value || '');
        updateBlockPreview('0', 'h2', document.getElementById('hero-h2')?.value || '');
        
        const heroType = document.querySelector('select[name="hero_type"]')?.value;
        if (heroType === 'sequence') {
            const folderId = document.getElementById('input-hero-folder')?.value;
            if (folderId) {
                fetch(`${window.rootPath}admin/media/folder/${folderId}/info`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.first_image) updateHeroBg(data.first_image);
                    });
            }
        } else {
            updateHeroBg(document.getElementById('input-hero')?.value);
        }

        // Dynamic Blocks Sync
        document.querySelectorAll('#blocks-wrapper .block-item').forEach(block => {
            const type = block.dataset.type;
            const idxInput = block.querySelector('input[name*="[type]"]');
            if(!idxInput) return;
            const idxMatch = idxInput.name.match(/\[(\d+)\]/);
            if(!idxMatch) return;
            const idx = idxMatch[1];
            
            if(type === 'intro_glass') {
                updateBlockPreview(idx, 'text', block.querySelector(`input[id^="textarea-"]`)?.value || '');
            } else if(type === 'text_large') {
                updateLargeTextPreview(idx);
            } else if(type === 'gallery_rail') {
                updateGalleryPreview(idx);
            } else if(type === 'carousel_adv') {
                updateCarouselPreview(idx);
            } else if(type === 'statement') {
                updateBlockPreview(idx, 'statement', block.querySelector(`textarea`)?.value || '');
            } else if(type === 'text_provocation') {
                updateBlockPreview(idx, 'text_provocation', block.querySelector(`textarea`)?.value || '');
                updateBlockPreview(idx, 'text_provocation_image', block.querySelector(`input[type="hidden"][name*="image"]`)?.value || '');
            }
        });
    }

    let blockIndex = {{ (old('content.blocks') && count(old('content.blocks')) > 0) ? max(array_keys(old('content.blocks'))) + 1 : (isset($blocks) && count($blocks) > 0 ? max(array_keys($blocks)) + 1 : 1) }};

    function setTheme(t) {
        document.getElementById('project-theme').value = t;
        document.querySelectorAll('.theme-option').forEach(el => el.classList.remove('active'));
        document.querySelector(`.theme-option[data-value="${t}"]`).classList.add('active');
        const preview = document.getElementById('preview-viewport');
        if(t === 'light') preview.style.background = "#eeeeee", preview.style.color = "#1a1a1a";
        else preview.style.background = "#1a1a1a", preview.style.color = "#eee";
    }

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

            else if(type === 'text_large') newItem.innerHTML = '<div class="TextLarge" style="padding: 20px;"></div>';
            else if(type === 'gallery_rail') newItem.innerHTML = '<div class="blog-scroll-strip"><div class="blog-scroll-strip__inner"><div class="blog-scroll-strip__rail"></div></div></div>';
            else if(type === 'carousel_adv') newItem.innerHTML = '<div class="carousel-wrapper-preview" style="display:flex; flex-direction:column; gap:15px;"></div>';
            
            list.appendChild(newItem);
        }

        // Initialize Rich Editors for this block
        if(type === 'intro_glass') {
            initRichEditor(`textarea-${blockIndex}-editor`, `textarea-${blockIndex}`);

        } else if(type === 'statement' || type === 'text_provocation' || type === 'custom_html') {
            // Textarea/HTML triggers it via oninput, no editor to init
        } else if(type === 'gallery_rail') {
            initRichEditor(`textarea-${blockIndex}-editor`, `textarea-${blockIndex}`);
        } else if(type === 'text_large') {
            // Start empty as requested
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

    function formatText(id, tag) {
        const editor = document.getElementById(id + '-editor');
        if (!editor) return;
        
        editor.focus();
        if (tag === 'b') {
            document.execCommand('bold', false, null);
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

    function initRichEditor(id, syncId) {
        const editor = document.getElementById(id);
        const textarea = document.getElementById(syncId);
        if (!editor || !textarea) return;

        editor.innerHTML = textarea.value;

        editor.addEventListener('input', () => {
            textarea.value = editor.innerHTML;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        });

        editor.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Insert BR securely
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const br = document.createElement("br");
                    range.deleteContents();
                    range.insertNode(br);
                    range.setStartAfter(br);
                    range.setEndAfter(br);
                    selection.removeAllRanges();
                    selection.addRange(range);
                    // Scroll to ensure visibility
                    editor.scrollTop = editor.scrollHeight;
                }
                // Update bindings
                editor.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (editor.classList.contains('no-bold') && (e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
            }
        });

        editor.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    }

    function updateCardPreview(field, val) {
        if(!window.cardShadow) return;
        
        if(field === 'title') {
            window.cardShadow.getElementById('card-title-preview').innerText = val || 'Título del Proyecto';
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
            const parts = dateVal.split('-');
            if(parts.length === 3) {
                 const date = new Date(parts[0], parts[1] - 1, parts[2]); 
                 const options = { year: 'numeric', month: 'long' };
                 elDate.innerText = date.toLocaleDateString('en-US', options);
            }
        }
        if(field === 'image') {
            if(val) {
                const baseUrl = window.rootPath || '/';
                const fullPath = val.startsWith('http') ? val : (baseUrl + 'storage/' + (val.startsWith('/') ? val.substring(1) : val));
                const imgEl = window.cardShadow.getElementById('card-img-preview');
                if(imgEl) imgEl.src = fullPath;
            }
        }
    }

    function updatePreview(scope, field, val) { if(scope === 'global' && field === 'title') updateBlockPreview('0', 'h1', val); }

    function updateBlockPreview(idx, field, val) {
        if(!window.previewShadow) return; 
        const p = window.previewShadow.getElementById('prev-block-' + idx);
        if(!p) return;
        const type = p.dataset.type;

        if(type === 'hero') {
            const h3 = p.querySelector('.h3');
            if(h3 && field === 'h3') h3.innerHTML = val;
            
            const h1 = p.querySelector('h1');
            if(h1 && field === 'h1') h1.innerHTML = val;
            
            const h2 = p.querySelector('h2');
            if(h2 && field === 'h2') h2.innerHTML = val;
        }
        else if(type === 'intro_glass') {
            let el = p.querySelector('.card');
            if(!el) { p.innerHTML = '<div class="card" style="padding: 2rem;"></div>'; el = p.querySelector('.card'); }
            el.innerHTML = val;
        }
        else if(type === 'phrase') {
            let el = p.querySelector('.span-resaltado');
            if(!el) { p.innerHTML = '<div class="span-resaltado" style="margin: 20px 0;"></div>'; el = p.querySelector('.span-resaltado'); }
            el.innerHTML = val;
        }
        else if(type === 'text_provocation') {
             const container = p.querySelector('.text-provocation');
             if(!container) { 
                 p.innerHTML = '<div class="text-provocation" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; padding: 2rem 1rem; background: #1a1a1a;"><img src="" style="width: 100%; display: none;"><div class="provoc-text"><h2 style="color: #eee;"></h2></div></div>'; 
             }
             // Handle text update
             if(field === 'text_provocation' || field === undefined) {
                 const h2 = p.querySelector('.provoc-text h2');
                 if(h2) h2.innerHTML = val;
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
             const container = p.querySelector('.custom-html-preview');
             if(!container) {
                 p.innerHTML = '<div class="custom-html-preview" style="padding: 20px; background: #f5f5f5; border: 2px dashed #999; text-align: center; color: #666; font-family: monospace;">Custom HTML Block<br><small>Preview disabled for security</small></div>';
             }
        }
        else if(type === 'statement') {
            let el = p.querySelector('.Statement');
            if(!el) { 
                p.innerHTML = '<div class="Statement" style="padding: 20px; text-align: center; font-size: 1.5rem; font-weight: bold;"></div>'; 
                el = p.querySelector('.Statement'); 
            }
            el.innerHTML = val;
        }
        else if(type === 'text_large') {
            updateLargeTextPreview(idx);
        }
    }

    function formatText(id, tag, className = '') {
        const editor = document.getElementById(id + '-editor');
        if (!editor) return;
        
        editor.focus();
        if (tag === 'b') {
            document.execCommand('bold', false, null);
        } else if (tag === 'ul') {
            document.execCommand('insertUnorderedList', false, null);
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
    function updateLargeTextPreview(idx) {
        if(!window.previewShadow) return;
        const p = window.previewShadow.getElementById('prev-block-' + idx);
        if(!p) return;

        const container = document.getElementById('text-large-elements-' + idx);
        if(!container) return;

        let html = '<div class="TextLarge" style="padding: 20px;">';
        const elements = container.querySelectorAll('.sub-element');
        elements.forEach(el => {
            const typeInput = el.querySelector('input[name*="[type]"]');
            const valueInput = el.querySelector('input[type="hidden"][name*="[value]"]');
            if(!typeInput || !valueInput) return;

            const type = typeInput.value;
            const editor = el.querySelector('.rich-editor');
            const value = editor ? editor.innerHTML : '';
            
            if(type === 'h2') {
                html += `<h2>${value}</h2>`;
            } else {
                html += `<div>${value}</div>`;
            }
        });
        html += '</div>';
        p.innerHTML = html;
    }

    function addLargeTextElement(blockIdx, type, value = '', subIdx = null) {
        const container = document.getElementById('text-large-elements-' + blockIdx);
        if(!container) return;
        
        if (subIdx === null) {
            subIdx = container.querySelectorAll('.sub-element').length + Math.floor(Math.random() * 1000);
        }
        
        const templateId = type === 'h2' ? 'tpl-text-large-h2' : 'tpl-text-large-text';
        const template = document.getElementById(templateId);
        let html = template.innerHTML.replace(/BLOCK_INDEX/g, blockIdx).replace(/SUB_INDEX/g, subIdx);
        container.insertAdjacentHTML('beforeend', html);
        
        const editorId = type === 'h2' ? `h2-${blockIdx}-${subIdx}` : `para-${blockIdx}-${subIdx}`;
        initRichEditor(editorId + '-editor', editorId);
        
        if (value) {
            const editor = document.getElementById(editorId + '-editor');
            const hidden = document.getElementById(editorId);
            if(editor) editor.innerHTML = value;
            if(hidden) hidden.value = value;
        }
        
        updateLargeTextPreview(blockIdx);
    }
    
    function removeLargeTextElement(btn, blockIdx) {
        if(confirm('¿Eliminar elemento?')) {
            btn.closest('.sub-element').remove();
            updateLargeTextPreview(blockIdx);
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

    function previewBlockImage(idx, input) {
        if (input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                if(!window.previewShadow) return;
                const s = window.previewShadow.getElementById('hero-preview-bg');
                if(s) s.style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(input.files[0]);
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
        const count = container.querySelectorAll('.gallery-item').length + Math.floor(Math.random() * 1000); // Random suffix to avoid id collision on re-adds
        const newItemHTML = document.getElementById('tpl-gallery-item').innerHTML.replace(/BLOCK_INDEX/g, blockIdx).replace(/ITEM_INDEX/g, count);
        container.insertAdjacentHTML('beforeend', newItemHTML);
        updateGalleryPreview(blockIdx);
    }

    function removeGalleryItem(btn, blockIdx) {
        btn.closest('.gallery-item').remove();
        updateGalleryPreview(blockIdx);
    }

    function updateGalleryPreview(idx) {
        if(!window.previewShadow) return;
        const p = window.previewShadow.getElementById('prev-block-' + idx);
        
        const phrase = document.getElementById(`textarea-${idx}`)?.value || '';
        const items = document.getElementById('gallery-items-' + idx).querySelectorAll('.gallery-item');
        const root = window.rootPath || '/';
        
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
    function toggleHeroType(type) {
        if(type === 'sequence') {
            document.getElementById('hero-static-fields').style.display = 'none';
            document.getElementById('hero-sequence-fields').style.display = 'block';
        } else {
            document.getElementById('hero-static-fields').style.display = 'block';
            document.getElementById('hero-sequence-fields').style.display = 'none';
        }
        syncAllPreviews();
    }

    document.addEventListener('DOMContentLoaded', () => { 
        initSortable(); 
        checkIntroGlassConstraint(); 
        
        // Local File Preview for Card Image
        const fileInput = document.querySelector('input[name="image"]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(ex) {
                        const preview = document.getElementById('preview-cover');
                        if (preview) {
                            preview.style.backgroundImage = `url('${ex.target.result}')`;
                            preview.innerHTML = '';
                        }
                        if (window.cardShadow) {
                            const cardImg = window.cardShadow.getElementById('card-img-preview');
                            if (cardImg) cardImg.src = ex.target.result;
                        }
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        // Listen for folder selection from media manager
        document.addEventListener('heroFolderSelected', function(e) {
            const data = e.detail;
            if (data && data.first_image) {
                updateHeroBg(data.first_image);
            }
        });
    });
</script>
@endsection
