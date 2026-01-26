@extends('layouts.app')

@section('title', 'Add New Project | INDI Lab')

@section('body-class', 'dark-theme')

@section('content')
<!-- External Libs for Builder -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
    .cms-container {
        max-width: 1000px;
        margin: 0 auto;
        padding-top: 50px;
        color: white;
    }
    .form-group {
        margin-bottom: 25px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #ddd;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        background: #222;
        border: 1px solid #444;
        color: white;
        border-radius: 4px;
        font-family: inherit;
    }
    .form-control:focus {
        border-color: #777;
        outline: none;
    }
    
    /* Block Builder UI Refinements */
    .blocks-container {
        margin-top: 40px;
        border-top: 2px solid #444;
        padding-top: 20px;
    }
    .block-item {
        background: #2a2a2a;
        border: 1px solid #444;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    .block-item:hover {
        border-color: #666;
    }
    .block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #333;
        padding: 12px 20px;
        cursor: move; /* Drag handle */
        border-bottom: 1px solid #444;
    }
    .block-header:hover {
        background: #3a3a3a;
    }
    .block-title {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85em;
        color: #aaa;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .block-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .block-btn {
        background: transparent;
        border: none;
        color: #888;
        cursor: pointer;
        font-size: 1.1em;
        transition: color 0.2s;
    }
    .block-btn:hover {
        color: #fff;
    }
    .block-btn.remove {
        color: #dc3545;
    }
    .block-body {
        padding: 20px;
        display: block;
    }
    .block-body.collapsed {
        display: none;
    }

    /* Add Block Grid UI */
    .add-block-area {
        background: #1e1e1e;
        padding: 20px;
        border-radius: 8px;
        border: 2px dashed #444;
        text-align: center;
        margin-top: 30px;
    }
    .add-block-title {
        color: #fff;
        margin-bottom: 20px;
        font-size: 1.1em;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .block-selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
    }
    .add-block-card {
        background: #2a2a2a;
        padding: 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .add-block-card:hover {
        background: #333;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    }
    .add-block-card i {
        font-size: 2em;
        color: #555;
    }
    .add-block-card span {
        font-size: 0.9em;
        color: #ccc;
        font-weight: bold;
    }
    .submit-btn {
        background: white;
        color: black;
        padding: 15px 40px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        font-size: 1.1em;
        margin-top: 30px;
        width: 100%;
        border-radius: 5px;
        transition: transform 0.2s;
    }
    .submit-btn:hover {
        transform: scale(1.01);
    }
    .drag-handle-icon {
        color: #555;
        margin-right: 10px;
    }
</style>

<section class="title-section-video" style="padding-top: 120px;">
    <div class="Title">
        <h1>New Project</h1>
    </div>
</section>

<section class="TextLarge" style="position: relative; z-index: 10; background-color: #1a1a1a; min-height: 100vh;">
    <div class="cms-container">
        
        @if ($errors->any())
            <div style="background-color: #5c1e23; color: #ffcccc; padding: 15px; border-radius: 5px; margin-bottom: 30px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('work.store') }}" method="POST" enctype="multipart/form-data" id="projectForm">
            @csrf
            
            <!-- Global Project Details -->
            <div class="block-item" style="border: 1px solid #555;">
                <div class="block-header" style="background: #222; cursor: default;">
                     <span class="block-title"><i class="fas fa-globe"></i> GLOBAL SETTINGS</span>
                </div>
                <div class="block-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Project Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="subtitle">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ old('subtitle') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="short_description">Short Summary (SEO)</label>
                        <textarea name="short_description" id="short_description" rows="2" class="form-control">{{ old('short_description') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="image">Main Cover Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- BLOCKS BUILDER AREA -->
            <div class="blocks-container">
                <h3 style="color: #fff; margin-bottom: 20px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-layer-group"></i> Content Blocks
                </h3>
                
                <div id="blocks-wrapper">
                    <!-- Dynamic Blocks will serve here -->
                </div>

                <!-- NEW ADD BLOCK GRID -->
                <div class="add-block-area">
                    <h4 class="add-block-title">Add Content Block</h4>
                    <div class="block-selection-grid">
                        <div class="add-block-card" onclick="addBlock('hero')">
                            <i class="fas fa-image"></i>
                            <span>Hero Header</span>
                        </div>
                        <div class="add-block-card" onclick="addBlock('intro')">
                            <i class="fas fa-align-left"></i>
                            <span>Intro Text</span>
                        </div>
                        <div class="add-block-card" onclick="addBlock('quote')">
                            <i class="fas fa-quote-right"></i>
                            <span>Quote</span>
                        </div>
                        <div class="add-block-card" onclick="addBlock('text')">
                            <i class="fas fa-paragraph"></i>
                            <span>Rich Text</span>
                        </div>
                        <div class="add-block-card" onclick="addBlock('gallery')">
                            <i class="fas fa-images"></i>
                            <span>Image Strip</span>
                        </div>
                        <div class="add-block-card" onclick="addBlock('carousel')">
                            <i class="fas fa-film"></i>
                            <span>Carousel</span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">Create Project</button>
        </form>
    </div>
</section>

<!-- Block Templates (Hidden) -->
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
    let blockIndex = 0;

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

        // Clone template content
        let clone = template.content.cloneNode(true);
        let html = clone.firstElementChild.outerHTML; // Get HTML string to replace placeholders

        // Replace INDEX with unique ID
        html = html.replace(/INDEX/g, blockIndex);
        
        // Append to DOM
        wrapper.insertAdjacentHTML('beforeend', html);
        
        // Scroll to new block
        wrapper.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        blockIndex++;
    }

    function removeBlock(btn, event) {
        event.stopPropagation(); // Prevent collapse toggle
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

    // Special handler for Carousel Slides nested
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
        
        // Insert before the add button
        btn.insertAdjacentHTML('beforebegin', slideHtml);
    }
</script>
@endsection
