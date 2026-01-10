<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'designation',
        'department',
        'qualification',
        'experience',
        'date_of_joining',
        'is_active',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_teacher_id');
    }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class);
    }
}
