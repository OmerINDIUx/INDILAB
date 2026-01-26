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
        $projects = Project::orderBy('created_at', 'desc')->get();
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
            'short_description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'content' => 'nullable|array', 
        ]);

        $contentData = $validated['content'] ?? [];
        $blocks = $contentData['blocks'] ?? [];

        // Recursive Block Processing for Files
        if (!empty($blocks)) {
            foreach ($blocks as $key => &$block) {
                $blockType = $block['type'] ?? 'unknown';
                
                // 1. Handle Hero Image
                if ($blockType === 'hero' && $request->hasFile("content.blocks.$key.data.image")) {
                    $path = $request->file("content.blocks.$key.data.image")->store('projects/blocks', 'public');
                    $block['data']['image'] = $path;
                }

                // 2. Handle Gallery Images (Multiple)
                if ($blockType === 'gallery' && $request->hasFile("content.blocks.$key.data.images")) {
                    $imagePaths = [];
                    foreach ($request->file("content.blocks.$key.data.images") as $img) {
                        $imagePaths[] = $img->store('projects/blocks', 'public');
                    }
                    $block['data']['images'] = $imagePaths;
                }

                // 3. Handle Carousel Slides (Nested)
                if ($blockType === 'carousel' && isset($block['data']['slides'])) {
                    foreach ($block['data']['slides'] as $sKey => &$slide) {
                        if ($request->hasFile("content.blocks.$key.data.slides.$sKey.image")) {
                            $path = $request->file("content.blocks.$key.data.slides.$sKey.image")->store('projects/blocks', 'public');
                            $slide['image'] = $path;
                        }
                    }
                    unset($slide); // Break reference
                }
            }
            unset($block); // Break reference
        }

        // Re-assign processed blocks to content
        $contentData['blocks'] = array_values($blocks); // Reset keys to be safe

        $project = new Project([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'short_description' => $validated['short_description'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'content' => $contentData, // Store the structured blocks
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $project->image_path = $path;
        }

        $project->save();

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
    public function edit(Project $project)
    {
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
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $project->fill($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $project->image_path = $path;
        }

        $project->save();

        return redirect()->route('work.show', $project)->with('success', 'Project updated successfully.');
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
