@extends('layouts.app')

@section('title', 'Edit Project | INDI Lab')

@section('body-class', 'dark-theme')

@section('content')
<!-- External Libs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    /* Reusing same styles from create.blade.php */
    .cms-container { max-width: 1000px; margin: 0 auto; padding-top: 50px; color: white; }
    .form-group { margin-bottom: 25px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: bold; color: #ddd; }
    .form-control { width: 100%; padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 4px; font-family: inherit; }
    .form-control:focus { border-color: #777; outline: none; }
    
    .blocks-container { margin-top: 40px; border-top: 2px solid #444; padding-top: 20px; }
    .block-item { background: #2a2a2a; border: 1px solid #444; border-radius: 8px; margin-bottom: 20px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
    .block-header { display: flex; justify-content: space-between; align-items: center; background: #333; padding: 12px 20px; cursor: move; border-bottom: 1px solid #444; }
    .block-title { font-weight: bold; text-transform: uppercase; font-size: 0.85em; color: #aaa; display: flex; align-items: center; gap: 10px; }
    .block-actions { display: flex; gap: 10px; align-items: center; }
    .block-btn { background: transparent; border: none; color: #888; cursor: pointer; font-size: 1.1em; }
    .block-btn:hover { color: #fff; }
    .block-btn.remove { color: #dc3545; }
    .block-body { padding: 20px; display: block; }
    .block-body.collapsed { display: none; }

    .add-block-area { background: #1e1e1e; padding: 20px; border-radius: 8px; border: 2px dashed #444; text-align: center; margin-top: 30px; }
    .add-block-title { color: #fff; margin-bottom: 20px; font-size: 1.1em; text-transform: uppercase; letter-spacing: 1px; }
    .block-selection-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
    .add-block-card { background: #2a2a2a; padding: 15px; border-radius: 6px; cursor: pointer; border: 1px solid #333; display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .add-block-card:hover { background: #333; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
    .add-block-card i { font-size: 2em; color: #555; }
    .add-block-card span { font-size: 0.9em; color: #ccc; font-weight: bold; }
    
    .submit-btn { background: white; color: black; padding: 15px 40px; border: none; cursor: pointer; font-weight: bold; font-size: 1.1em; margin-top: 30px; width: 100%; border-radius: 5px; }
    .drag-handle-icon { color: #555; margin-right: 10px; }
</style>

<section class="title-section-video" style="padding-top: 150px;">
    <div class="Title">
        <h1>Edit Project: {{ $project->title }}</h1>
    </div>
</section>

<section class="TextLarge" style="position: relative; z-index: 10; background-color: #1a1a1a; min-height: 100vh;">
    <div class="cms-container">
        <form action="{{ route('work.update', $project) }}" method="POST" enctype="multipart/form-data" id="projectForm">
            @csrf
            @method('PUT')
            
            <!-- Global Project Details -->
            <div class="block-item" style="border: 1px solid #555;">
                <div class="block-header" style="background: #222; cursor: default;">
                     <span class="block-title"><i class="fas fa-globe"></i> GLOBAL SETTINGS</span>
                </div>
                <div class="block-body">
                    <div class="form-group">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $project->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $project->subtitle) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Short Summary (SEO)</label>
                        <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $project->short_description) }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Cover Image</label>
                         @if($project->image_path)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ asset('storage/' . $project->image_path) }}" style="max-height: 100px; border-radius: 4px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- BLOCKS BUILDER AREA -->
            <div class="blocks-container">
                <h3 style="color: #fff; margin-bottom: 20px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-layer-group"></i> Content Blocks
                </h3>
                
                <div id="blocks-wrapper">
                    <!-- PRE-POPULATE BLOCKS -->
                    @if(isset($project->content['blocks']) && is_array($project->content['blocks']))
                        @foreach($project->content['blocks'] as $index => $block)
                            @php $type = $block['type']; $data = $block['data'] ?? []; @endphp
                            
                            <div class="block-item" data-type="{{ $type }}">
                                <input type="hidden" name="content[blocks][{{ $index }}][type]" value="{{ $type }}">
                                
                                <div class="block-header" onclick="toggleBlock(this)">
                                    <span class="block-title">
                                        <i class="fas fa-bars drag-handle-icon"></i> 
                                        @if($type=='hero') <i class="fas fa-image"></i> &nbsp; HERO SECTION 
                                        @elseif($type=='intro') <i class="fas fa-align-left"></i> &nbsp; INTRO TEXT
                                        @elseif($type=='quote') <i class="fas fa-quote-right"></i> &nbsp; HIGHLIGHT QUOTE
                                        @elseif($type=='text') <i class="fas fa-paragraph"></i> &nbsp; RICH TEXT
                                        @elseif($type=='gallery') <i class="fas fa-images"></i> &nbsp; IMAGE GRID
                                        @elseif($type=='carousel') <i class="fas fa-film"></i> &nbsp; CAROUSEL
                                        @else BLOCK @endif
                                    </span>
                                    <div class="block-actions">
                                        <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                                        <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>

                                <div class="block-body">
                                    {{-- HERO --}}
                                    @if($type === 'hero')
                                        <div class="form-group">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="content[blocks][{{ $index }}][data][title]" class="form-control" value="{{ $data['title'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Subtitle</label>
                                            <input type="text" name="content[blocks][{{ $index }}][data][subtitle]" class="form-control" value="{{ $data['subtitle'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Background Image</label>
                                            @if(isset($data['image'])) <img src="{{ asset('storage/' . $data['image']) }}" style="height:50px; display:block; margin-bottom:5px;"> @endif
                                            <input type="hidden" name="content[blocks][{{ $index }}][data][image]" value="{{ $data['image'] ?? '' }}">
                                            <input type="file" name="content[blocks][{{ $index }}][data][image]" class="form-control">
                                        </div>
                                    @endif

                                    {{-- INTRO --}}
                                    @if($type === 'intro')
                                        <div class="form-group">
                                            <label class="form-label">Intro Paragraph</label>
                                            <textarea name="content[blocks][{{ $index }}][data][text]" rows="4" class="form-control">{{ $data['text'] ?? '' }}</textarea>
                                        </div>
                                    @endif

                                    {{-- QUOTE --}}
                                    @if($type === 'quote')
                                        <div class="form-group">
                                            <label class="form-label">Quote Text</label>
                                            <textarea name="content[blocks][{{ $index }}][data][text]" rows="3" class="form-control">{{ $data['text'] ?? '' }}</textarea>
                                        </div>
                                    @endif

                                    {{-- TEXT --}}
                                    @if($type === 'text')
                                        <div class="form-group">
                                            <label class="form-label">Section Title</label>
                                            <input type="text" name="content[blocks][{{ $index }}][data][title]" class="form-control" value="{{ $data['title'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Content</label>
                                            <textarea name="content[blocks][{{ $index }}][data][content]" rows="8" class="form-control">{{ $data['content'] ?? '' }}</textarea>
                                        </div>
                                    @endif

                                    {{-- GALLERY --}}
                                    @if($type === 'gallery')
                                        <div class="form-group">
                                            <label class="form-label">Upload Images</label>
                                            <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
                                                @if(isset($data['images']))
                                                    @foreach($data['images'] as $img)
                                                        <img src="{{ asset('storage/' . $img) }}" style="height:60px; border-radius:4px;">
                                                        <input type="hidden" name="content[blocks][{{ $index }}][data][images][]" value="{{ $img }}">
                                                    @endforeach
                                                @endif
                                            </div>
                                            <input type="file" name="content[blocks][{{ $index }}][data][images][]" multiple class="form-control">
                                        </div>
                                    @endif

                                    {{-- CAROUSEL --}}
                                    @if($type === 'carousel')
                                        <div class="carousel-slides-container" style="padding-left: 20px; border-left: 2px solid #555;">
                                             @if(isset($data['slides']))
                                                @foreach($data['slides'] as $sIndex => $slide)
                                                    <div class="slide-item" style="background:#333; padding:15px; margin-bottom:15px; border-radius:4px; position:relative; border:1px solid #444;">
                                                        <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:10px; right:10px; background:none; border:none; color:#dc3545; cursor:pointer; font-size:1.2em;">&times;</button>
                                                        
                                                        <div class="form-group" style="margin-bottom:10px;">
                                                            <label style="display:block; color:#aaa; font-size:0.8em; margin-bottom:5px;">Image</label>
                                                            @if(isset($slide['image'])) <img src="{{ asset('storage/' . $slide['image']) }}" style="height:40px; display:block; margin-bottom:5px;"> @endif
                                                            <input type="hidden" name="content[blocks][{{ $index }}][data][slides][{{ $sIndex }}][image]" value="{{ $slide['image'] ?? '' }}">
                                                            <input type="file" name="content[blocks][{{ $index }}][data][slides][{{ $sIndex }}][image]" class="form-control" style="padding:5px;">
                                                        </div>
                                                        <div class="form-group" style="margin-bottom:10px;">
                                                            <input type="text" name="content[blocks][{{ $index }}][data][slides][{{ $sIndex }}][title]" class="form-control" value="{{ $slide['title'] ?? '' }}" placeholder="Slide Title" style="padding:8px; background:#222; color:white; border:1px solid #555;">
                                                        </div>
                                                        <div class="form-group" style="margin-bottom:10px;">
                                                            <textarea name="content[blocks][{{ $index }}][data][slides][{{ $sIndex }}][description]" class="form-control" rows="2" placeholder="Description" style="padding:8px; background:#222; color:white; border:1px solid #555;">{{ $slide['description'] ?? '' }}</textarea>
                                                        </div>
                                                         <div class="form-group" style="margin-bottom:0px;">
                                                            <input type="text" name="content[blocks][{{ $index }}][data][slides][{{ $sIndex }}][link]" class="form-control" value="{{ $slide['link'] ?? '' }}" placeholder="Link URL (Optional)" style="padding:8px; background:#222; color:white; border:1px solid #555;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                             @endif
                                             <button type="button" class="add-btn" style="background:#444; color:white; padding: 8px 15px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;" onclick="addCarouselSlide(this, '{{ $index }}')">+ Add Slide</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- ADD BLOCK GRID -->
                <div class="add-block-area">
                    <h4 class="add-block-title">Add Content Block</h4>
                    <div class="block-selection-grid">
                        <div class="add-block-card" onclick="addBlock('hero')"> <i class="fas fa-image"></i> <span>Hero Header</span> </div>
                        <div class="add-block-card" onclick="addBlock('intro')"> <i class="fas fa-align-left"></i> <span>Intro Text</span> </div>
                        <div class="add-block-card" onclick="addBlock('quote')"> <i class="fas fa-quote-right"></i> <span>Quote</span> </div>
                        <div class="add-block-card" onclick="addBlock('text')"> <i class="fas fa-paragraph"></i> <span>Rich Text</span> </div>
                        <div class="add-block-card" onclick="addBlock('gallery')"> <i class="fas fa-images"></i> <span>Image Strip</span> </div>
                        <div class="add-block-card" onclick="addBlock('carousel')"> <i class="fas fa-film"></i> <span>Carousel</span> </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">Update Project</button>
        </form>
    </div>
</section>

<!-- Block Templates (SAME AS CREATE.BLADE.PHP) -->
<template id="tpl-hero">
    <div class="block-item" data-type="hero">
        <input type="hidden" name="content[blocks][INDEX][type]" value="hero">
        <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-image"></i> &nbsp; HERO SECTION</span>
            <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="content[blocks][INDEX][data][title]" class="form-control" placeholder="Large Title">
            </div>
            <div class="form-group">
                <label class="form-label">Subtitle</label>
                <input type="text" name="content[blocks][INDEX][data][subtitle]" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Background Image</label>
                <input type="file" name="content[blocks][INDEX][data][image]" class="form-control" accept="image/*">
            </div>
        </div>
    </div>
</template>

<template id="tpl-intro">
    <div class="block-item" data-type="intro">
        <input type="hidden" name="content[blocks][INDEX][type]" value="intro">
        <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-align-left"></i> &nbsp; INTRO TEXT</span>
            <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Intro Paragraph</label>
                <textarea name="content[blocks][INDEX][data][text]" rows="4" class="form-control" placeholder="Start typing the main introduction..."></textarea>
            </div>
        </div>
    </div>
</template>

<template id="tpl-quote">
    <div class="block-item" data-type="quote">
        <input type="hidden" name="content[blocks][INDEX][type]" value="quote">
         <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-quote-right"></i> &nbsp; HIGHLIGHT QUOTE</span>
             <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Quote Text</label>
                <textarea name="content[blocks][INDEX][data][text]" rows="3" class="form-control" placeholder="Enter the key question or quote..."></textarea>
            </div>
        </div>
    </div>
</template>

<template id="tpl-text">
    <div class="block-item" data-type="text">
        <input type="hidden" name="content[blocks][INDEX][type]" value="text">
        <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-paragraph"></i> &nbsp; TEXT SECTION</span>
             <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Section Title</label>
                <input type="text" name="content[blocks][INDEX][data][title]" class="form-control" placeholder="Optional Title">
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content[blocks][INDEX][data][content]" rows="8" class="form-control" placeholder="Detailed content..."></textarea>
            </div>
        </div>
    </div>
</template>

<template id="tpl-gallery">
    <div class="block-item" data-type="gallery">
        <input type="hidden" name="content[blocks][INDEX][type]" value="gallery">
        <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-images"></i> &nbsp; IMAGE GRID</span>
             <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="form-group">
                <label class="form-label">Upload Images</label>
                <input type="file" name="content[blocks][INDEX][data][images][]" multiple class="form-control" accept="image/*">
                <p style="color:#888; font-size: 0.9em; margin-top:5px;">Select multiple files to create a horizontal scroll strip.</p>
            </div>
        </div>
    </div>
</template>

<template id="tpl-carousel">
    <div class="block-item" data-type="carousel">
        <input type="hidden" name="content[blocks][INDEX][type]" value="carousel">
        <div class="block-header" onclick="toggleBlock(this)">
            <span class="block-title"><i class="fas fa-bars drag-handle-icon"></i> <i class="fas fa-film"></i> &nbsp; CAROUSEL</span>
             <div class="block-actions">
                <button type="button" class="block-btn" title="Collapse"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="block-btn remove" onclick="removeBlock(this, event)" title="Remove"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="block-body">
            <div class="carousel-slides-container" style="padding-left: 20px; border-left: 2px solid #555;">
                 <!-- Slides added via JS -->
                 <button type="button" class="add-btn" style="background:#444; color:white; padding: 8px 15px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;" onclick="addCarouselSlide(this, 'INDEX')">+ Add Slide</button>
            </div>
        </div>
    </div>
</template>

<script>
    // Initialize blockIndex based on existing blocks
    let blockIndex = {{ isset($project->content['blocks']) ? count($project->content['blocks']) : 0 }};

    // Initialize Sortable
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('blocks-wrapper');
        const sortable = Sortable.create(el, {
            handle: '.block-header', // Drag handle
            animation: 150,
            ghostClass: 'sortable-ghost',
        });
    });

    function addBlock(type) {
        const wrapper = document.getElementById('blocks-wrapper');
        const template = document.getElementById('tpl-' + type);
        
        if(!template) return;

        let clone = template.content.cloneNode(true);
        let html = clone.firstElementChild.outerHTML; 

        html = html.replace(/INDEX/g, blockIndex);
        
        wrapper.insertAdjacentHTML('beforeend', html);
        wrapper.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        blockIndex++;
    }

    function removeBlock(btn, event) {
        event.stopPropagation();
        if(confirm('Delete this block?')) {
            btn.closest('.block-item').remove();
        }
    }

    function toggleBlock(header) {
        const body = header.nextElementSibling;
        const icon = header.querySelector('.block-actions .fa-chevron-up, .block-actions .fa-chevron-down');
        
        if (body.classList.contains('collapsed')) {
            body.classList.remove('collapsed');
            if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
        } else {
            body.classList.add('collapsed');
            if(icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
        }
    }

    function addCarouselSlide(btn, blockIdxParam) {
        const container = btn.parentElement;
        const existingSlides = container.querySelectorAll('.slide-item').length;
        
        const slideHtml = `
            <div class="slide-item" style="background:#333; padding:15px; margin-bottom:15px; border-radius:4px; position:relative; border:1px solid #444;">
                <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:10px; right:10px; background:none; border:none; color:#dc3545; cursor:pointer; font-size:1.2em;">&times;</button>
                <div class="form-group" style="margin-bottom:10px;">
                    <label style="display:block; color:#aaa; font-size:0.8em; margin-bottom:5px;">Image</label>
                    <input type="file" name="content[blocks][${blockIdxParam}][data][slides][${existingSlides}][image]" class="form-control" style="padding:5px;" accept="image/*">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <input type="text" name="content[blocks][${blockIdxParam}][data][slides][${existingSlides}][title]" class="form-control" placeholder="Slide Title" style="padding:8px; background:#222; color:white; border:1px solid #555;">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <textarea name="content[blocks][${blockIdxParam}][data][slides][${existingSlides}][description]" class="form-control" rows="2" placeholder="Description" style="padding:8px; background:#222; color:white; border:1px solid #555;"></textarea>
                </div>
                 <div class="form-group" style="margin-bottom:0px;">
                    <input type="text" name="content[blocks][${blockIdxParam}][data][slides][${existingSlides}][link]" class="form-control" placeholder="Link URL (Optional)" style="padding:8px; background:#222; color:white; border:1px solid #555;">
                </div>
            </div>
        `;
        
        btn.insertAdjacentHTML('beforebegin', slideHtml);
    }
</script>
@endsection
