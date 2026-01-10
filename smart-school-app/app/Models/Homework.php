<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
    use SoftDeletes;

    protected $table = 'homework';

    protected $fillable = [
        'title',
        'description',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'attachment',
        'submission_date',
        'is_active',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getDueDateAttribute()
    {
        return $this->submission_date;
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }
}
