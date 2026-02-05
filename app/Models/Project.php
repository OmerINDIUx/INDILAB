<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'sticky_title',
        'short_description',
        'tags',
        'theme',
        'hero_type',
        'hero_folder_id',
        'content',
        'image_path',
        'meta_title',
        'meta_description',
        'published_at',
        'coming_soon',
        'category',
        'meta_keywords',
        'badge_color',
        'draft_content',
        'draft_last_editor_id',
        'draft_updated_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'coming_soon' => 'boolean',
        'content' => 'array',
        'draft_content' => 'array',
        'draft_updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function heroFolder()
    {
        return $this->belongsTo(MediaFolder::class, 'hero_folder_id');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'draft_last_editor_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
