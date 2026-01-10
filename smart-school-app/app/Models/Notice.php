<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'notice_date',
        'expiry_date',
        'is_active',
        'audience',
        'created_by',
    ];

    protected $casts = [
        'notice_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];
}
