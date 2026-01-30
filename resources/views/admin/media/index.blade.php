@extends('layouts.admin')

@section('title', 'Media Library | INDI Lab Admin')

@push('css')
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
    }
    .media-item {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 1;
        transition: transform 0.2s;
    }
    .media-item:hover {
        transform: translateY(-5px);
        border-color: var(--dashboard-primary);
    }
    .media-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .media-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        padding: 10px;
        font-size: 0.75rem;
        transform: translateY(100%);
        transition: transform 0.2s;
    }
    .media-item:hover .media-info {
        transform: translateY(0);
    }
    .media-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .media-item:hover .media-actions {
        opacity: 1;
    }
    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
    }
    .btn-delete { background: #dc3545; color: #fff; }
    .btn-view { background: #007bff; color: #fff; }
</style>
@endpush

@section('admin_title', 'Media Library')
@section('admin_subtitle', 'Manage all uploaded files and assets')

@section('admin_actions')
<button class="btn-primary" onclick="window.openMediaManager()" style="background: var(--dashboard-primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
    <i class="fas fa-upload"></i> Upload Media
</button>
@endsection

@section('admin_content')
<div class="media-grid">
    @foreach($media as $item)
    <div class="media-item">
        @if(str_contains($item->mime_type, 'image'))
            <img src="{{ asset('storage/' . $item->path) }}" class="media-preview">
        @else
            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #222;">
                <i class="fas fa-file" style="font-size: 3rem; color: #444;"></i>
            </div>
        @endif
        
        <div class="media-actions">
            <a href="{{ asset('storage/' . $item->path) }}" target="_blank" class="action-btn btn-view"><i class="fas fa-external-link-alt"></i></a>
            <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn btn-delete"><i class="fas fa-trash"></i></button>
            </form>
        </div>
        
        <div class="media-info">
            <div style="font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->filename }}</div>
            <div style="color: #aaa; font-size: 0.65rem;">{{ number_format($item->size / 1024 / 1024, 2) }} MB</div>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top: 30px; display: flex; justify-content: center;">
    {{ $media->links() }}
</div>
@endsection
