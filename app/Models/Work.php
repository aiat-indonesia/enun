<?php

namespace App\Models;

use App\Enums\WorkStatus;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Work extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\WorkFactory> */
    use HasFactory, HasTags, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'summary',
        'author_id',
        'contributors',
        'place_id',
        'creation_year',
        'metadata',
        'status',
        'visibility',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => WorkType::class,
            'status' => WorkStatus::class,
            'summary' => 'array',
            'contributors' => 'array',
            'creation_year' => 'array',
            'metadata' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'author_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(Instance::class);
    }

    public function agents(): MorphToMany
    {
        return $this->morphToMany(Agent::class, 'agentable', 'agent_role')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('manuscripts')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/tiff'])
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
            ])
            ->useDisk('public');
    }
}
