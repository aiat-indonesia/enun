<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Agent extends Model
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'birth_date',
        'death_date',
        'biography',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
            'metadata' => 'array',
        ];
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
