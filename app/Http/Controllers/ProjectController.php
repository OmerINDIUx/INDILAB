<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // Constructor removed - middleware defined in routes

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('lastEditor')
            ->orderBy('coming_soon', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'sticky_title' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'theme' => 'required|in:dark,light',
            'short_description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'image_path' => 'nullable|string',
            'category' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'badge_color' => 'nullable|string',
            'published_at' => 'nullable|date',
            'coming_soon' => 'nullable|boolean',
            'content' => 'nullable|array', 
        ]);

        $contentData = $validated['content'] ?? [];
        $blocks = $contentData['blocks'] ?? [];

        if (!empty($blocks)) {
            $blocks = $this->processBlockFiles($request, $blocks);
        }

        $contentData['blocks'] = array_values($blocks);

        $project = new Project([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'sticky_title' => $validated['sticky_title'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'theme' => $validated['theme'] ?? 'dark',
            'short_description' => $validated['short_description'] ?? null,
            'category' => $validated['category'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'badge_color' => $validated['badge_color'] ?? 'cat-grad-1',
            'published_at' => $validated['published_at'] ?? null,
            'coming_soon' => $request->boolean('coming_soon'),
        ]);

        if ($request->action === 'save') {
            $project->draft_content = $contentData;
            $project->content = ['blocks' => []]; // Empty live content
            $project->draft_last_editor_id = auth()->id();
            $project->draft_updated_at = now();
        } else {
            $project->content = $contentData;
            $project->draft_content = null;
            $project->draft_last_editor_id = null;
            $project->draft_updated_at = null;
        }
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $project->image_path = $path;
        } elseif ($request->filled('image_path')) {
            $project->image_path = $request->image_path;
        }

        $project->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'project' => $project,
                'redirect' => route('work.edit', $project)
            ]);
        }

        return redirect()->route('work.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project)
    {
        if ($request->get('use_draft') && $project->draft_content) {
            $project->content = $project->draft_content;
        }
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'sticky_title' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'theme' => 'required|in:dark,light',
            'short_description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'image_path' => 'nullable|string',
            'category' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'badge_color' => 'nullable|string',
            'published_at' => 'nullable|date',
            'coming_soon' => 'nullable|boolean',
            'content' => 'nullable|array',
        ]);

        $contentData = $validated['content'] ?? [];
        $blocks = $contentData['blocks'] ?? [];

        // Note: We rely on hidden inputs in the form for persistence. 
        // If a file is uploaded, processBlockFiles overwrites the hidden input value.
        // If no file, the hidden input (string path) is preserved.

        if (!empty($blocks)) {
            $blocks = $this->processBlockFiles($request, $blocks);
        }

        $contentData['blocks'] = array_values($blocks);
        
        // Update basic fields manually to avoid overwriting content
        $project->title = $validated['title'];
        $project->subtitle = $validated['subtitle'] ?? null;
        $project->sticky_title = $validated['sticky_title'] ?? null;
        $project->tags = $validated['tags'] ?? null;
        $project->theme = $validated['theme'] ?? 'dark';
        $project->short_description = $validated['short_description'] ?? null;
        $project->category = $validated['category'] ?? null;
        $project->meta_keywords = $validated['meta_keywords'] ?? null;
        $project->badge_color = $validated['badge_color'] ?? 'cat-grad-1';
        $project->published_at = $validated['published_at'] ?? null;
        $project->coming_soon = $request->boolean('coming_soon');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $project->image_path = $path;
        } elseif ($request->filled('image_path')) {
            $project->image_path = $request->image_path;
        }

        if ($request->action === 'save') {
            $project->draft_content = $contentData;
            $project->draft_last_editor_id = auth()->id();
            $project->draft_updated_at = now();
        } else {
            $project->content = $contentData;
            $project->draft_content = null; // Clear draft on publish
            $project->draft_last_editor_id = null;
            $project->draft_updated_at = null;
        }

        $project->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cambios guardados como borrador.']);
        }

        return redirect()->route('work.show', $project)->with('success', 'Project updated and published successfully.');
    }

    private function processBlockFiles($request, $blocks)
    {
        foreach ($blocks as $key => &$block) {
            $blockType = $block['type'] ?? 'unknown';
            
            // 1. Single Image Blocks
            // Look for 'image_file' upload. If found, upload and set 'image'. 
            // Also create Media record.
            if (in_array($blockType, ['hero', 'intro_glass'])) {
                if ($request->hasFile("content.blocks.$key.data.image_file")) {
                    $file = $request->file("content.blocks.$key.data.image_file");
                    $path = $file->store('projects/blocks', 'public');
                    $block['data']['image'] = $path;
                    
                    // Register in Media Library
                    \App\Models\Media::create([
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            // 2. Gallery Blocks
            // Gallery handles multiple files. We assume input name="...[images_files][]"
            // 2. Gallery Blocks (Refined for Slot/Order preservation)
            if ($blockType === 'gallery_rail') {
                $finalImages = [];
                
                // New Approach: 'gallery_items' loop (Order preserved)
                if (isset($block['data']['gallery_items']) && is_array($block['data']['gallery_items'])) {
                    foreach ($block['data']['gallery_items'] as $i => $item) {
                        // Check for upload
                        if ($request->hasFile("content.blocks.$key.data.gallery_items.$i.image_file")) {
                            $file = $request->file("content.blocks.$key.data.gallery_items.$i.image_file");
                            $path = $file->store('projects/blocks', 'public');
                            $finalImages[] = $path;

                            \App\Models\Media::create([
                                'filename' => $file->getClientOriginalName(),
                                'path' => $path,
                                'mime_type' => $file->getMimeType(),
                                'size' => $file->getSize(),
                            ]);
                        } elseif (!empty($item['image'])) {
                            // Keep existing/selected
                            $finalImages[] = $item['image'];
                        }
                    }
                } 
                // Fallback for Legacy/Bulk method (if still used)
                elseif (isset($block['data']['images'])) {
                     $finalImages = $block['data']['images']; // Keep existing
                     // Handle bulk uploads if any (Old method)
                     if ($request->hasFile("content.blocks.$key.data.images_files")) {
                        foreach ($request->file("content.blocks.$key.data.images_files") as $img) {
                             $path = $img->store('projects/blocks', 'public');
                             $finalImages[] = $path;
                        }
                     }
                }

                $block['data']['images'] = $finalImages;
                unset($block['data']['gallery_items']); // Clean up
            }

            // 3. Carousel Blocks
            if ($blockType === 'carousel_adv' && isset($block['data']['slides'])) {
                foreach ($block['data']['slides'] as $sKey => &$slide) {
                    if ($request->hasFile("content.blocks.$key.data.slides.$sKey.image_file")) {
                        $file = $request->file("content.blocks.$key.data.slides.$sKey.image_file");
                        $path = $file->store('projects/blocks', 'public');
                        $slide['image'] = $path;

                        \App\Models\Media::create([
                            'filename' => $file->getClientOriginalName(),
                            'path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ]);
                    }
                }
            }
        }
        return $blocks;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('work.index')->with('success', 'Project deleted successfully.');
    }
}
