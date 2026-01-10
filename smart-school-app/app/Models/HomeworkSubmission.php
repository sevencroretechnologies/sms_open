<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
    protected $table = 'homework_submissions';

    protected $fillable = [
        'homework_id',
        'student_id',
        'file_path',
        'submitted_at',
        'is_late',
        'resubmission_count',
        'marks',
        'graded_by',
        'remarks',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
        'resubmission_count' => 'integer',
        'marks' => 'decimal:2',
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
