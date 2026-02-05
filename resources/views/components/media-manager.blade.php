<!-- Media Manager Modal -->
<div id="media-modal" class="media-modal">
    <div class="media-modal-content">
        <div class="media-header">
            <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
                <h3 style="margin: 0; font-size: 1.2rem; letter-spacing: 1px; color: #000; font-weight: 800;">MEDIA LIBRARY</h3>
                <div id="modal-breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #999; margin-left: 20px;">
                    <span style="cursor: pointer;" onclick="fetchMediaInModal(null)">Root</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <button type="button" onclick="promptNewFolderInModal()" class="btn-action-modal" style="background: #f0f7ff; color: #007bff; border: 1px solid #cce5ff; padding: 8px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-folder-plus"></i> New Folder
                </button>
                <button type="button" onclick="closeMediaModal()" class="close-btn">&times;</button>
            </div>
        </div>
        <div class="media-body">
            <div class="media-upload-zone" id="upload-zone">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag & Drop or Click to Upload (Bulk supported)</p>
                <input type="file" id="media-upload-input" multiple hidden>
            </div>
            <div class="media-gallery" id="media-gallery">
                <!-- Media items injected here via JS -->
                <div class="loading-spinner">Loading...</div>
            </div>
        </div>
        <div class="media-footer">
            <button type="button" class="btn-cancel" onclick="closeMediaModal()">Cancel</button>
        </div>
    </div>
</div>

<style>
    .media-modal {
        position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10000;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: all 0.3s;
        backdrop-filter: blur(5px);
    }
    .media-modal.open { opacity: 1; pointer-events: auto; }
    
    .media-modal-content {
        background: #fff; width: 1000px; max-width: 95%; height: 85vh;
        border-radius: 24px; display: flex; flex-direction: column; overflow: hidden;
        box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: #000;
    }
    .media-modal.open .media-modal-content { transform: scale(1); }
    
    .media-header { padding: 25px 35px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fff; }
    .close-btn { background: none; border: none; font-size: 2.5rem; cursor: pointer; color: #ccc; transition: color 0.2s; line-height: 0.8; }
    .close-btn:hover { color: #f00; }
    
    .btn-action-modal { transition: all 0.2s; padding: 8px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; border: 1px solid #cce5ff; }
    .btn-action-modal:hover { background: #007bff !important; color: #fff !important; }

    .media-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #fafafa; }
    
    .media-upload-zone {
        padding: 25px; background: #fdfdfd; border-bottom: 2px dashed #eee;
        text-align: center; cursor: pointer; transition: all 0.3s;
        color: #666; margin: 0;
    }
    .media-upload-zone:hover { background: #f0f7ff; border-color: #007bff; color: #007bff; }
    .media-upload-zone i { font-size: 2.2rem; margin-bottom: 10px; opacity: 0.6; display: block; }
    .media-upload-zone p { margin: 0; font-size: 0.95rem; font-weight: 500; }
    
    .media-gallery {
        flex: 1; padding: 30px; overflow-y: auto;
        display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        grid-auto-rows: 1fr;
        gap: 25px; align-content: start;
    }
    
    .media-modal .media-item, .media-modal .folder-item-modal {
        width: 100%; min-height: 150px;
        aspect-ratio: 1 / 1; background: #fff; border-radius: 16px; overflow: hidden;
        position: relative; cursor: pointer; border: 2px solid #f0f0f0;
        transition: all 0.3s ease; display: flex; flex-direction: column;
    }
    
    .media-modal .media-item:hover, .media-modal .folder-item-modal:hover { 
        border-color: #007bff; transform: translateY(-5px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    }
    
    .media-modal .media-item img { width: 100%; height: 100%; object-fit: cover; }
    
    .media-modal .folder-item-modal {
        background: rgba(255, 193, 7, 0.08); border-color: rgba(255, 193, 7, 0.2);
        align-items: center; justify-content: center;
        text-align: center; color: #856404;
    }

    .media-modal .folder-item-modal.drag-over {
        background: rgba(0, 123, 255, 0.1);
        border: 2px dashed #007bff;
        transform: scale(1.05);
    }

    .media-modal .folder-item-modal i { font-size: 3.5rem; color: #ffc107; margin-bottom: 8px; filter: drop-shadow(0 4px 8px rgba(255,193,7,0.2)); pointer-events: none; }
    .media-modal .folder-item-modal span { font-size: 0.85rem; font-weight: 700; padding: 0 12px; overflow: hidden; text-overflow: ellipsis; width: 100%; white-space: nowrap; pointer-events: none; }

    .media-footer { padding: 25px 35px; border-top: 1px solid #eee; text-align: right; background: #fff; }
    .btn-cancel { padding: 12px 30px; border: 1px solid #eee; background: #fff; border-radius: 12px; cursor: pointer; font-weight: 600; color: #666; transition: all 0.2s; }
    .btn-cancel:hover { background: #f5f5f5; color: #000; }

    .media-modal .loading-spinner {
        grid-column: 1 / -1;
        padding: 60px;
        text-align: center;
        color: #999;
    }
    
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .fa-spin-custom { animation: spin 1s linear infinite; }

    /* Bulk Upload Progress Styles */
    .upload-progress-item {
        background: #fff; border: 1px solid #eee; padding: 10px 15px; border-radius: 8px; margin-bottom: 8px;
        display: flex; flex-direction: column; gap: 5px;
    }
    .progress-bar-container { height: 6px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: #007bff; width: 0%; transition: width 0.2s; }
</style>

<script>
    if (typeof window.managerCurrentFolderId === 'undefined') {
        window.currentTargetInputId = null;
        window.currentPreviewId = null;
        window.managerCurrentFolderId = null;
    }

    let modalDraggedId = null;
    let modalDraggedType = null;

    function openMediaModal(inputId, previewId) {
        window.currentTargetInputId = inputId;
        window.currentPreviewId = previewId;
        window.managerCurrentFolderId = null;
        document.getElementById('media-modal').classList.add('open');
        fetchMediaInModal(null);
    }

    function closeMediaModal() {
        document.getElementById('media-modal').classList.remove('open');
    }

    function fetchMediaInModal(folderId = null) {
        const gallery = document.getElementById('media-gallery');
        gallery.innerHTML = '<div class="loading-spinner"><i class="fas fa-circle-notch fa-spin-custom fa-3x"></i><br><br><span style="font-weight:600; font-size:1.1rem">Loading your assets...</span></div>';
        
        window.managerCurrentFolderId = folderId;
        
        const url = new URL("{{ route('admin.media.index') }}");
        if(folderId) url.searchParams.append('folder_id', folderId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            renderGalleryInModal(data);
            updateModalBreadcrumbs(data.breadcrumb || []);
        })
        .catch(err => {
            console.error('Fetch error:', err);
            gallery.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #dc3545; padding: 40px; font-weight:700;">Failed to load library.</div>';
        });
    }

    function updateModalBreadcrumbs(path = []) {
        const breadcrumb = document.getElementById('modal-breadcrumb');
        if(!breadcrumb) return;
        
        breadcrumb.innerHTML = '<span style="cursor: pointer;" onclick="fetchMediaInModal(null)">Root</span>';
        
        if (path && path.length > 0) {
            path.forEach((folder, index) => {
                breadcrumb.innerHTML += '<i class="fas fa-chevron-right" style="font-size: 0.7rem; opacity: 0.5;"></i>';
                const span = document.createElement('span');
                span.innerText = folder.name;
                span.style.cursor = 'pointer';
                if (index === path.length - 1) {
                    span.style.color = '#000';
                    span.style.fontWeight = '700';
                }
                span.onclick = () => fetchMediaInModal(folder.id);
                breadcrumb.appendChild(span);
            });
        }
    }

    function promptNewFolderInModal() {
        const name = prompt("Enter folder name:");
        if (!name) return;

        fetch("{{ route('admin.media.folder.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                parent_id: window.managerCurrentFolderId
            })
        })
        .then(res => res.json())
        .then(data => {
            fetchMediaInModal(window.managerCurrentFolderId);
        });
    }

    function renderGalleryInModal(response) {
        const gallery = document.getElementById('media-gallery');
        gallery.innerHTML = '';
        
        const folders = response.folders || [];
        const items = response.media || [];
        
        updateModalUploadLabel();
        
        if(window.managerCurrentFolderId) {
            const upDiv = document.createElement('div');
            upDiv.className = 'folder-item-modal drop-zone-modal';
            upDiv.dataset.id = response.parent_id || "";
            upDiv.innerHTML = '<i class="fas fa-level-up-alt" style="color:#aaa"></i><span style="color:#999">Back to Parent</span>';
            upDiv.onclick = () => fetchMediaInModal(response.parent_id);
            gallery.appendChild(upDiv);
        }

        folders.forEach(folder => {
            const div = document.createElement('div');
            div.className = 'folder-item-modal drop-zone-modal';
            div.dataset.id = folder.id;
            div.draggable = true;
            div.innerHTML = `<i class="fas fa-folder"></i><span>${folder.name}</span>`;
            
            div.onclick = (e) => {
                if(e.target === div || e.target.tagName === 'I' || e.target.tagName === 'SPAN') {
                    fetchMediaInModal(folder.id);
                }
            };
            setupModalDraggable(div, folder.id, 'folder');
            gallery.appendChild(div);
        });

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'media-item';
            div.dataset.id = item.id;
            div.draggable = true;
            const storagePath = (window.rootPath || '/') + 'storage/' + item.path;
            
            if(item.mime_type && item.mime_type.includes('image')) {
                div.innerHTML = `<img src="${storagePath}" loading="lazy" onerror="this.src='https://via.placeholder.com/150?text=Error'">`;
            } else {
                div.innerHTML = `<div style="flex:1; display:flex; align-items:center; justify-content:center; background:#f8f8f8;"><i class="fas fa-file-alt fa-3x" style="color:#ddd;"></i></div><span style="font-size:0.75rem; font-weight:600; padding:10px; text-align:center; overflow:hidden; text-overflow:ellipsis; background:#fff; border-top:1px solid #eee; width:100%">${item.filename}</span>`;
            }
            
            div.onclick = () => selectMediaFromModal(item);
            setupModalDraggable(div, item.id, 'media');
            gallery.appendChild(div);
        });

        setupModalDropZones();

        if(folders.length === 0 && items.length === 0) {
            gallery.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #bbb; padding: 60px; font-weight:500;">No assets found in this folder.</div>';
        }
    }

    function setupModalDraggable(el, id, type) {
        el.addEventListener('dragstart', (e) => {
            modalDraggedId = id;
            modalDraggedType = type;
            el.style.opacity = '0.4';
            e.dataTransfer.setData('text/plain', id);
        });
        el.addEventListener('dragend', () => {
            el.style.opacity = '1';
            document.querySelectorAll('.drop-zone-modal').forEach(dz => dz.classList.remove('drag-over'));
        });
    }

    function setupModalDropZones() {
        document.querySelectorAll('.drop-zone-modal').forEach(dz => {
            dz.addEventListener('dragover', (e) => {
                e.preventDefault();
                if(modalDraggedId != dz.dataset.id) {
                    dz.classList.add('drag-over');
                }
            });
            dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('drag-over');
                const targetId = dz.dataset.id === "" ? null : dz.dataset.id;
                if(modalDraggedId && modalDraggedId != targetId) {
                    moveItemInModal(modalDraggedId, modalDraggedType, targetId);
                }
            });
        });
    }

    function moveItemInModal(id, type, targetFolderId) {
        const payload = { target_folder_id: targetFolderId };
        if (type === 'media') payload.media_ids = [id];
        else payload.folder_ids = [id];

        fetch("{{ route('admin.media.move') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => { if(data.success) fetchMediaInModal(window.managerCurrentFolderId); });
    }

    function updateModalUploadLabel() {
        const uploadLabel = document.getElementById('current-upload-folder-name');
        if(uploadLabel) uploadLabel.innerText = window.managerCurrentFolderId ? "this folder" : "Root";
    }

    function selectMediaFromModal(item) {
        if(window.currentTargetInputId) {
            const input = document.getElementById(window.currentTargetInputId);
            if (input) { input.value = item.path; input.dispatchEvent(new Event('change')); }
            if(window.currentPreviewId) {
                const preview = document.getElementById(window.currentPreviewId);
                if (preview) {
                    const storagePath = (window.rootPath || '/') + 'storage/' + item.path;
                    preview.style.backgroundImage = `url('${storagePath}')`;
                    preview.innerHTML = '';
                    preview.style.backgroundColor = 'transparent';
                }
            }
        }
        closeMediaModal();
    }

    function handleModalUpload(e) {
        const files = Array.from(e.target.files);
        if(files.length === 0) return;

        const dz = document.getElementById('upload-zone');
        const originalHTML = dz.innerHTML;
        dz.style.pointerEvents = 'none';
        
        dz.innerHTML = `
            <div id="multi-upload-container" style="width: 100%; max-width: 500px; margin: 0 auto; text-align: left; max-height: 200px; overflow-y: auto; padding: 10px;">
                <p style="margin-bottom: 12px; font-weight: 800; text-transform:uppercase; letter-spacing:1px; color:#007bff; text-align:center;">
                    Uploading ${files.length} assets...
                </p>
                <div id="upload-items-list"></div>
            </div>
        `;

        const list = document.getElementById('upload-items-list');
        let completed = 0;

        files.forEach(file => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'upload-progress-item';
            itemDiv.innerHTML = `
                <div style="display:flex; justify-content:space-between; font-size:0.75rem; font-weight:600;">
                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:200px;">${file.name}</span>
                    <span class="status-percent">0%</span>
                </div>
                <div class="progress-bar-container"><div class="progress-bar-fill"></div></div>
            `;
            list.appendChild(itemDiv);

            const fill = itemDiv.querySelector('.progress-bar-fill');
            const percentText = itemDiv.querySelector('.status-percent');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', "{{ csrf_token() }}");
            if(window.managerCurrentFolderId) formData.append('folder_id', window.managerCurrentFolderId);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ route('admin.media.store') }}", true);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    fill.style.width = percent + '%';
                    percentText.innerText = percent + '%';
                }
            };

            xhr.onload = () => {
                completed++;
                itemDiv.style.borderColor = '#28a745';
                itemDiv.style.background = '#f8fff9';
                percentText.innerText = 'DONE';
                percentText.style.color = '#28a745';
                if(completed === files.length) {
                    setTimeout(() => { fetchMediaInModal(window.managerCurrentFolderId); dz.style.pointerEvents = 'auto'; dz.innerHTML = originalHTML; }, 1000);
                }
            };

            xhr.onerror = () => {
                completed++;
                itemDiv.style.borderColor = '#dc3545';
                itemDiv.style.background = '#fff5f5';
                percentText.innerText = 'FAILED';
                percentText.style.color = '#dc3545';
                if(completed === files.length) {
                    setTimeout(() => { dz.innerHTML = originalHTML; dz.style.pointerEvents = 'auto'; }, 3000);
                }
            };

            xhr.send(formData);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modalDropzone = document.getElementById('upload-zone');
        const uploadInput = document.getElementById('media-upload-input');
        if(modalDropzone && uploadInput) {
            modalDropzone.addEventListener('click', () => uploadInput.click());
            uploadInput.addEventListener('change', handleModalUpload);
        }
    });

    window.openMediaManager = function() {
        openMediaModal(null, null);
    };
</script>
