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
        'slug',
        'type',
        'parent_id',
        'latitude',
        'longitude',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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

    public function works(): HasMany
    {
        return $this->hasMany(Work::class, 'place_id');
    }

    public function birthPlaceAgents(): HasMany
    {
        return $this->hasMany(Agent::class, 'birth_place');
    }

    public function deathPlaceAgents(): HasMany
    {
        return $this->hasMany(Agent::class, 'death_place');
    }

    public function itemsAsLocation(): HasMany
    {
        return $this->hasMany(Item::class, 'location');
    }

    public function instancesAsPublication(): HasMany
    {
        return $this->hasMany(Instance::class, 'publication_place_id');
    }
}
