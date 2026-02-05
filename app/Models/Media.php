<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'path', 'mime_type', 'size', 'folder_id'];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}
