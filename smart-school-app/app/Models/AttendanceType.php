<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AttendanceType Model
 * 
 * Defines attendance types (present, absent, late, etc.)
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $color
 * @property bool $is_present
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AttendanceType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'color',
        'is_present',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_present' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the attendances for this type.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'attendance_type_id');
    }

    /**
     * Scope a query to only include active attendance types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this type represents present.
     */
    public function isPresent(): bool
    {
        return $this->is_present;
    }

    /**
     * Check if this type is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
