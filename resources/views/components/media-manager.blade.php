<!-- Media Manager Modal -->
<div id="media-modal" class="media-modal hidden">
    <div class="media-modal-content">
        <div class="media-header">
            <h3>Media Library</h3>
            <button type="button" onclick="closeMediaModal()" class="close-btn">&times;</button>
        </div>
        <div class="media-body">
            <div class="media-upload-zone" id="upload-zone">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag & Drop or Click to Upload</p>
                <input type="file" id="media-upload-input" hidden>
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
        position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .media-modal.open { opacity: 1; pointer-events: auto; }
    
    .media-modal-content {
        background: #fff; width: 900px; max-width: 90%; height: 80vh;
        border-radius: 12px; display: flex; flex-direction: column; overflow: hidden;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    }
    
    .media-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .media-header h3 { margin: 0; font-weight: 800; text-transform: uppercase; }
    .close-btn { background: none; border: none; font-size: 2rem; cursor: pointer; line-height: 1; }
    
    .media-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    
    .media-upload-zone {
        padding: 30px; background: #f8f8f8; border-bottom: 1px dashed #ddd;
        text-align: center; cursor: pointer; transition: background 0.2s;
    }
    .media-upload-zone:hover { background: #f0f0f0; }
    .media-upload-zone i { font-size: 2rem; color: #999; margin-bottom: 10px; }
    
    .media-gallery {
        flex: 1; padding: 20px; overflow-y: auto;
        display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px; align-content: start;
    }
    
    .media-item {
        aspect-ratio: 1; background: #eee; border-radius: 8px; overflow: hidden;
        position: relative; cursor: pointer; border: 2px solid transparent;
    }
    .media-item:hover { border-color: #000; }
    .media-item.selected { border-color: #000; box-shadow: 0 0 0 4px rgba(0,0,0,0.1); }
    .media-item img { width: 100%; height: 100%; object-fit: cover; }
    
    .media-footer { padding: 15px; border-top: 1px solid #eee; text-align: right; background: #fff; }
    .btn-cancel { padding: 10px 20px; border: 1px solid #ddd; background: #fff; border-radius: 6px; cursor: pointer; }
</style>

<script>
    let currentTargetInputId = null;
    let currentPreviewId = null;

    function openMediaModal(inputId, previewId) {
        currentTargetInputId = inputId;
        currentPreviewId = previewId;
        document.getElementById('media-modal').classList.add('open');
        fetchMedia();
    }

    function closeMediaModal() {
        document.getElementById('media-modal').classList.remove('open');
        currentTargetInputId = null;
        currentPreviewId = null;
    }

    function fetchMedia() {
        const gallery = document.getElementById('media-gallery');
        gallery.innerHTML = '<div class="loading-spinner">Loading library...</div>';
        
        // Use the global rootPath variable if available, otherwise fallback
        const baseUrl = window.rootPath || '/';
        
        fetch("{{ route('admin.media.index') }}")
            .then(res => res.json())
            .then(data => renderGallery(data));
    }

    function renderGallery(items) {
        const gallery = document.getElementById('media-gallery');
        gallery.innerHTML = '';
        
        if(items.length === 0) {
            gallery.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #999; padding: 20px;">No images found. Upload one!</div>';
            return;
        }

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'media-item';
            // Correctly construct path using rootPath
            // If item.path is "media/foo.jpg", we want "http://localhost/INDI_LabPHP/public/storage/media/foo.jpg"
            const storagePath = (window.rootPath || '/') + 'storage/' + item.path;
            
            div.innerHTML = `<img src="${storagePath}" loading="lazy" onerror="this.src='https://via.placeholder.com/150?text=Error'">`;
            div.onclick = () => selectMedia(item);
            gallery.appendChild(div);
        });
    }

    function selectMedia(item) {
        if(currentTargetInputId) {
            // Set hidden input value (path)
            const input = document.getElementById(currentTargetInputId);
            input.value = item.path;
            input.dispatchEvent(new Event('change'));
            
            // Update Preview
            if(currentPreviewId) {
                const preview = document.getElementById(currentPreviewId);
                const storagePath = (window.rootPath || '/') + 'storage/' + item.path;
                preview.style.backgroundImage = `url('${storagePath}')`;
                preview.innerHTML = ''; // Clear placeholder text
            }
            
            const inputEl = document.getElementById(currentTargetInputId);
            const wrapper = inputEl.closest('.media-selector-wrapper');
            if(wrapper) {
                const fileInput = wrapper.querySelector('input[type="file"]');
                if(fileInput) fileInput.value = '';
            }
        }
        closeMediaModal();
    }

    // Header Upload Logic
    const dropzone = document.getElementById('upload-zone');
    dropzone.addEventListener('click', () => document.getElementById('media-upload-input').click());
    document.getElementById('media-upload-input').addEventListener('change', handleUpload);

    function handleUpload(e) {
        const file = e.target.files[0];
        if(!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', "{{ csrf_token() }}");

        // UI Feedback: Progress Bar
        dropzone.style.pointerEvents = 'none';
        dropzone.innerHTML = `
            <div style="width: 100%; max-width: 200px; margin: 0 auto;">
                <p style="margin-bottom: 5px; font-weight: 600;">Uploading...</p>
                <div style="height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                    <div id="upload-progress" style="width: 0%; height: 100%; background: #000; transition: width 0.3s;"></div>
                </div>
            </div>
        `;

        // Simulate progress since fetch doesn't support progress events natively without XHR
        // Ideally use axios or XHR for real progress, but visual feedback is good enough
        let progress = 10;
        const interval = setInterval(() => {
            progress += Math.random() * 20;
            if(progress > 90) progress = 90;
            document.getElementById('upload-progress').style.width = progress + '%';
        }, 200);

        fetch("{{ route('admin.media.store') }}", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(media => {
            clearInterval(interval);
            document.getElementById('upload-progress').style.width = '100%';
            setTimeout(() => {
                fetchMedia(); // Refresh gallery
                dropzone.style.pointerEvents = 'auto';
                dropzone.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><p>Drag & Drop or Click to Upload</p>';
            }, 500);
        })
        .catch(err => {
            clearInterval(interval);
            console.error(err);
            dropzone.style.pointerEvents = 'auto';
            dropzone.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:red"></i><p style="color:red">Error uploading file</p>';
        });
    }
</script>
