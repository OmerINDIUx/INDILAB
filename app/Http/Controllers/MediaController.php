<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->get('folder_id');
        
        $folders = MediaFolder::where('parent_id', $folderId)->get();
        $mediaQuery = Media::where('folder_id', $folderId)->latest();

        $breadcrumb = [];
        if ($folderId) {
            $current = MediaFolder::find($folderId);
            $temp = $current;
            while($temp) {
                array_unshift($breadcrumb, ['id' => $temp->id, 'name' => $temp->name]);
                $temp = $temp->parent;
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            $media = $mediaQuery->get();
            return response()->json([
                'folders' => $folders,
                'media' => $media,
                'parent_id' => $folderId ? MediaFolder::find($folderId)?->parent_id : null,
                'breadcrumb' => $breadcrumb,
            ]);
        }

        $media = $mediaQuery->paginate(24);
        $currentFolder = $folderId ? MediaFolder::find($folderId) : null;
        
        return view('admin.media.index', compact('media', 'folders', 'folderId', 'currentFolder', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max raw
            'folder_id' => 'nullable|exists:media_folders,id'
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        $isImage = str_contains($mime, 'image') && !str_contains($mime, 'svg');
        
        if ($isImage) {
            // Optimize image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            
            // Resize if exceeds 1920px width/height
            $image->scaleDown(width: 1920, height: 1080);
            
            // Generate unique path
            $savePath = 'media/' . uniqid() . '.' . $extension;
            
            // Encode based on type or convert to webp/jpg for better size
            // For now, let's just compress the original format
            if ($extension === 'png') {
                $encoded = $image->toPng();
            } elseif ($extension === 'webp') {
                $encoded = $image->toWebp(80);
            } else {
                $encoded = $image->toJpeg(80);
                $savePath = str_replace('.'.$extension, '.jpg', $savePath);
            }
            
            Storage::disk('public')->put($savePath, (string) $encoded);
            $size = Storage::disk('public')->size($savePath);
            $path = $savePath;
        } else {
            $path = $file->store('media', 'public');
            $size = $file->getSize();
        }
        
        $media = Media::create([
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $mime,
            'size' => $size,
            'folder_id' => $request->folder_id
        ]);

        return response()->json($media);
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id'
        ]);

        $folder = MediaFolder::create($request->only('name', 'parent_id'));
        return response()->json($folder);
    }

    public function move(Request $request)
    {
        $request->validate([
            'media_ids' => 'nullable|array',
            'folder_ids' => 'nullable|array',
            'target_folder_id' => 'nullable|exists:media_folders,id'
        ]);

        if ($request->has('media_ids')) {
            Media::whereIn('id', $request->media_ids)->update(['folder_id' => $request->target_folder_id]);
        }

        if ($request->has('folder_ids')) {
            // Prevent moving a folder into itself
            $folderIds = array_diff($request->folder_ids, [$request->target_folder_id]);
            MediaFolder::whereIn('id', $folderIds)->update(['parent_id' => $request->target_folder_id]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Media $media)
    {
        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }
        $media->delete();
        return response()->json(['success' => true]);
    }

    public function destroyFolder(MediaFolder $folder)
    {
        // Recursive deletion or just moving files to parent? 
        // User asked for "organize", usually deleting folder deletes contents or moves them.
        // Let's delete content for now (standard file system behavior) or set folder_id to null.
        // Cascade is handled by migration for child folders, but not for media (set null).
        $folder->delete();
        return response()->json(['success' => true]);
    }
}
