<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeesType extends Model
{
    protected $table = 'fees_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function feesMasters(): HasMany
    {
        return $this->hasMany(FeesMaster::class, 'fees_type_id');
    }
}
