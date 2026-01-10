<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeesMaster extends Model
{
    protected $table = 'fees_masters';

    protected $fillable = [
        'fees_type_id',
        'fees_group_id',
        'academic_session_id',
        'amount',
        'due_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function feesType(): BelongsTo
    {
        return $this->belongsTo(FeesType::class, 'fees_type_id');
    }

    public function feesGroup(): BelongsTo
    {
        return $this->belongsTo(FeesGroup::class, 'fees_group_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function allotments(): HasMany
    {
        return $this->hasMany(FeesAllotment::class, 'fees_master_id');
    }
}
