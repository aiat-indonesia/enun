<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Agent extends Model
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'birth_place',
        'birth_date',
        'death_place',
        'death_date',
        'biography',
        'roles',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
            'roles' => 'array',
            'metadata' => 'array',
        ];
    }

    public function birthPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'birth_place');
    }

    public function deathPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'death_place');
    }

    public function worksAsAuthor(): HasMany
    {
        return $this->hasMany(Work::class, 'author_id');
    }

    public function instancesAsPublisher(): HasMany
    {
        return $this->hasMany(Instance::class, 'publisher_id');
    }

    public function itemsAsCurrentHolder(): HasMany
    {
        return $this->hasMany(Item::class, 'current_holder');
    }

    public function works(): MorphToMany
    {
        return $this->morphedByMany(Work::class, 'agentable', 'agent_role')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function instances(): MorphToMany
    {
        return $this->morphedByMany(Instance::class, 'agentable', 'agent_role')
            ->withPivot('role')
            ->withTimestamps();
    }
}
