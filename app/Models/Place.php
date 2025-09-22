<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    /** @use HasFactory<\Database\Factories\PlaceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'lat',
        'lng',
        'geojson_polygon',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'geojson_polygon' => 'array',
            'metadata' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Place::class, 'parent_id');
    }

    public function worksAsPrimary(): HasMany
    {
        return $this->hasMany(Work::class, 'primary_place_id');
    }

    public function works(): HasMany
    {
        return $this->hasMany(Work::class, 'primary_place_id');
    }

    public function instancesAsPublication(): HasMany
    {
        return $this->hasMany(Instance::class, 'publication_place_id');
    }
}
