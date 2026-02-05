@extends('layouts.admin')

@section('title', 'Media Library | INDI Lab Admin')

@push('css')
<style>
    :root {
        --folder-bg: rgba(255, 193, 7, 0.1);
        --folder-border: #ffc107;
    }
    .media-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .media-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--dashboard-card-bg);
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid var(--dashboard-border);
        margin-bottom: 10px;
    }
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: var(--dashboard-text-muted);
    }
    .breadcrumb-nav a {
        color: var(--dashboard-accent);
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-nav a:hover {
        color: #fff;
    }
    .breadcrumb-nav i {
        font-size: 0.7rem;
        opacity: 0.5;
    }

    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        grid-auto-rows: 1fr;
        gap: 25px;
        align-content: start;
    }
    
    .media-item, .folder-item {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        width: 100%;
        min-height: 200px;
        aspect-ratio: 1 / 1;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }
    
    .media-item[draggable="true"], .folder-item[draggable="true"] {
        cursor: grab;
    }

    .folder-item {
        background: var(--folder-bg);
        border-color: rgba(255, 193, 7, 0.2);
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: var(--dashboard-text);
        text-align: center;
        padding: 20px;
    }
    
    .folder-item i {
        font-size: 4.5rem;
        color: var(--folder-border);
        margin-bottom: 10px;
        transition: transform 0.3s;
        filter: drop-shadow(0 5px 15px rgba(255,193,7,0.2));
    }
    
    .folder-item:hover {
        border-color: var(--folder-border);
        transform: translateY(-5px);
        background: rgba(255, 193, 7, 0.15);
    }
    
    .folder-item:hover i {
        transform: scale(1.1) rotate(-5deg);
    }
    
    .folder-item.drag-over {
        background: rgba(255, 193, 7, 0.3);
        border: 2px dashed var(--folder-border);
        transform: scale(1.05);
        z-index: 10;
    }

    .media-item:hover {
        transform: translateY(-5px);
        border-color: var(--dashboard-accent);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    
    .media-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    
    .media-item:hover .media-preview {
        transform: scale(1.1);
    }
    
    .media-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.95));
        padding: 30px 15px 15px;
        font-size: 0.8rem;
        transform: translateY(100%);
        transition: transform 0.3s;
        pointer-events: none;
        z-index: 2;
    }
    
    .media-item:hover .media-info {
        transform: translateY(0);
    }
    
    .media-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        opacity: 0;
        transition: all 0.3s;
        transform: translateX(10px);
        z-index: 3;
    }
    
    .media-item:hover .media-actions, .folder-item:hover .media-actions {
        opacity: 1;
        transform: translateX(0);
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        backdrop-filter: blur(8px);
        transition: all 0.2s;
        color: #fff;
    }
    
    .btn-delete { background: rgba(220, 53, 69, 0.7); }
    .btn-delete:hover { background: #dc3545; transform: scale(1.1); }
    .btn-view { background: rgba(0, 123, 255, 0.7); }
    .btn-view:hover { background: #007bff; transform: scale(1.1); }

    .empty-folder {
        grid-column: 1 / -1;
        padding: 80px 20px;
        text-align: center;
        background: rgba(255,255,255,0.02);
        border-radius: 20px;
        border: 2px dashed var(--dashboard-border);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .media-item, .folder-item {
        animation: slideIn 0.5s ease-out backwards;
    }
</style>
@endpush

@section('admin_title', 'Media Library')
@section('admin_subtitle', 'Organize and manage your digital assets')

@section('admin_actions')
<div style="display: flex; gap: 12px;">
    <button class="btn" onclick="showNewFolderModal()" style="background: rgba(255, 193, 7, 0.15); color: var(--folder-border); border: 1px solid rgba(255,193,7,0.3); padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-folder-plus"></i> New Folder
    </button>
    <button class="btn-primary" onclick="window.openMediaManager()" style="background: var(--dashboard-accent); color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-upload"></i> Upload Media
    </button>
</div>
@endsection

@section('admin_content')
<div class="media-container">
    <div class="media-toolbar">
        <div class="breadcrumb-nav">
            <a href="{{ route('admin.media.index') }}"><i class="fas fa-home" style="font-size: 1.1rem; color: #fff;"></i></a>
            @if(count($breadcrumb) > 0)
                <i class="fas fa-chevron-right"></i>
                @foreach($breadcrumb as $bc)
                    <a href="{{ route('admin.media.index', ['folder_id' => $bc['id']]) }}">{{ $bc['name'] }}</a>
                    @if(!$loop->last) <i class="fas fa-chevron-right"></i> @endif
                @endforeach
            @else
                <i class="fas fa-chevron-right"></i>
                <span>Root</span>
            @endif
        </div>
        <div class="stats" style="font-size: 0.85rem; color: var(--dashboard-text-muted); font-weight: 500;">
            <span style="color: var(--folder-border);">{{ $folders->count() }}</span> Folders &bull; <span style="color: var(--dashboard-accent);">{{ $media->total() }}</span> Files
        </div>
    </div>

    <div class="media-grid">
        @if($folderId)
            @php
                $parentFolderId = $currentFolder ? $currentFolder->parent_id : null;
            @endphp
            <a href="{{ route('admin.media.index', ['folder_id' => $parentFolderId]) }}" class="folder-item" style="background: rgba(255,255,255,0.03); border-color: var(--dashboard-border);">
                <i class="fas fa-arrow-left" style="color: var(--dashboard-text-muted); font-size: 3rem;"></i>
                <div style="font-weight: 600; color: var(--dashboard-text-muted);">Go Back</div>
            </a>
        @endif

        @foreach($folders as $folder)
        <div class="folder-item draggable-folder drop-zone" 
             draggable="true" 
             data-id="{{ $folder->id }}"
             onclick="window.location.href='{{ route('admin.media.index', ['folder_id' => $folder->id]) }}'">
            <i class="fas fa-folder"></i>
            <div style="font-weight: 600; overflow: hidden; text-overflow: ellipsis; width: 100%; white-space: nowrap; padding: 0 10px;">{{ $folder->name }}</div>
            
            <div class="media-actions">
                <button onclick="event.stopPropagation(); deleteFolder({{ $folder->id }}, '{{ $folder->name }}')" class="action-btn btn-delete" title="Delete Folder">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @endforeach

        @foreach($media as $item)
        <div class="media-item draggable-media" draggable="true" data-id="{{ $item->id }}">
            @if(str_contains($item->mime_type, 'image'))
                <img src="{{ asset('storage/' . $item->path) }}" class="media-preview" loading="lazy">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #111;">
                    <i class="fas fa-file-alt" style="font-size: 3.5rem; color: #333;"></i>
                </div>
            @endif
            
            <div class="media-actions">
                <a href="{{ asset('storage/' . $item->path) }}" target="_blank" class="action-btn btn-view" title="View / Open"><i class="fas fa-external-link-alt"></i></a>
                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete" title="Delete File"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            
            <div class="media-info">
                <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px;">{{ $item->filename }}</div>
                <div style="color: #aaa; font-size: 0.7rem;">{{ strtoupper($item->mime_type) }} &bull; {{ number_format($item->size / 1024 / 1024, 2) }} MB</div>
            </div>
        </div>
        @endforeach

        @if($folders->count() == 0 && $media->count() == 0)
        <div class="empty-folder">
            <i class="fas fa-folder-open" style="font-size: 5rem; opacity: 0.05; margin-bottom: 20px; display: block; color: #fff;"></i>
            <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--dashboard-text-muted);">Folder is empty</h3>
            <p style="color: var(--dashboard-text-muted); max-width: 400px; margin: 0 auto;">This folder doesn't contain any files or sub-folders yet. Use the buttons above to add content.</p>
        </div>
        @endif
    </div>

    @if($media->hasPages())
    <div style="margin-top: 40px; display: flex; justify-content: center;">
        {{ $media->appends(['folder_id' => $folderId])->links() }}
    </div>
    @endif
</div>

<!-- New Folder Modal -->
<div id="new-folder-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: var(--dashboard-card-bg); border: 1px solid var(--dashboard-border); padding: 35px; border-radius: 20px; width: 450px; max-width: 90%; box-shadow: 0 30px 60px rgba(0,0,0,0.5);">
        <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">New Folder</h3>
        <p style="color: var(--dashboard-text-muted); font-size: 0.9rem; margin-bottom: 25px;">Enter a name for your new directory.</p>
        
        <input type="text" id="folder-name-input" placeholder="e.g. Project Assets" onkeyup="if(event.key === 'Enter') createFolder()"
               style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid var(--dashboard-border); background: #000; color: #fff; margin-bottom: 25px; outline: none; font-size: 1rem;">
        
        <div style="display: flex; justify-content: flex-end; gap: 15px;">
            <button onclick="hideNewFolderModal()" style="padding: 12px 25px; border-radius: 12px; cursor: pointer; border: 1px solid var(--dashboard-border); background: transparent; color: #fff; font-weight: 600; transition: all 0.2s;">Cancel</button>
            <button onclick="createFolder()" style="padding: 12px 30px; border-radius: 12px; cursor: pointer; border: none; background: var(--dashboard-accent); color: #fff; font-weight: 700; transition: all 0.2s; box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);">Create Folder</button>
        </div>
    </div>
</div>

<script>
    const currentFolderId = {{ $folderId ?? 'null' }};

    function showNewFolderModal() {
        document.getElementById('new-folder-modal').style.display = 'flex';
        document.getElementById('folder-name-input').focus();
    }

    function hideNewFolderModal() {
        document.getElementById('new-folder-modal').style.display = 'none';
        document.getElementById('folder-name-input').value = '';
    }

    function createFolder() {
        const name = document.getElementById('folder-name-input').value;
        if(!name) return;

        fetch("{{ route('admin.media.folder.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                parent_id: currentFolderId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.id) {
                window.location.reload();
            }
        })
        .catch(err => console.error('Error creating folder:', err));
    }

    function deleteFolder(id, name) {
        if(confirm(`Are you sure you want to delete the folder "${name}"? Items inside will be moved to the parent directory.`)) {
            fetch(`{{ url('admin/media/folder') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(() => window.location.reload())
            .catch(err => console.error('Error deleting folder:', err));
        }
    }

    // --- Drag and Drop Logic ---
    let draggedElement = null;
    let draggedType = null;
    let draggedId = null;

    function initDraggable() {
        document.querySelectorAll('.draggable-media, .draggable-folder').forEach(el => {
            el.addEventListener('dragstart', (e) => {
                draggedElement = el;
                draggedId = el.dataset.id;
                draggedType = el.classList.contains('draggable-media') ? 'media' : 'folder';
                
                // Visual feedback
                el.style.opacity = '0.4';
                el.style.transform = 'scale(0.95)';
                
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', draggedId);
                
                // Create custom ghost image if desired (optional)
            });

            el.addEventListener('dragend', () => {
                el.style.opacity = '1';
                el.style.transform = '';
                document.querySelectorAll('.drop-zone').forEach(dz => dz.classList.remove('drag-over'));
            });
        });

        document.querySelectorAll('.drop-zone').forEach(el => {
            el.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (el !== draggedElement) {
                    el.classList.add('drag-over');
                    e.dataTransfer.dropEffect = 'move';
                }
            });

            el.addEventListener('dragleave', () => {
                el.classList.remove('drag-over');
            });

            el.addEventListener('drop', (e) => {
                e.preventDefault();
                el.classList.remove('drag-over');
                const targetId = el.dataset.id;
                
                if (draggedId && targetId && draggedId !== targetId) {
                    moveItem(draggedId, draggedType, targetId);
                }
            });
        });
    }

    function moveItem(id, type, targetFolderId) {
        const payload = {
            target_folder_id: targetFolderId
        };
        
        if (type === 'media') {
            payload.media_ids = [id];
        } else {
            payload.folder_ids = [id];
        }

        // Show loading state
        if(draggedElement) draggedElement.style.display = 'none';

        fetch("{{ route('admin.media.move') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Refresh to show move
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Error moving item:', err);
            if(draggedElement) draggedElement.style.display = 'block';
        });
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', initDraggable);

    // Patch Media Manager to support folder-aware uploads
    window.addEventListener('load', () => {
        if (typeof window.openMediaManager === 'function') {
            const originalOpenMediaManager = window.openMediaManager;
            window.openMediaManager = function() {
                if (typeof openMediaModal === 'function') {
                     window.managerCurrentFolderId = currentFolderId;
                }
                originalOpenMediaManager();
            };
        }
    });
</script>
@endsection
