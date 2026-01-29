<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'url',
        'page_title',
        'referrer',
        'user_agent',
        'ip_address',
        'session_id',
        'user_id',
        'country',
        'city',
        'device_type',
        'browser',
        'time_on_page',
        'scroll_depth',
    ];

    protected $casts = [
        'time_on_page' => 'integer',
        'scroll_depth' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
