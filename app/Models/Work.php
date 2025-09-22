<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'slug',
        'title',
        'subtitle',
        'description',
        'languages',
        'summary',
        'type',
        'status',
        'primary_place_id',
        'metadata',
        'alternative_titles',
        'external_identifiers',
        'seller_links',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'metadata' => 'array',
            'alternative_titles' => 'array',
            'external_identifiers' => 'array',
            'seller_links' => 'array',
        ];
    }

    public function primaryPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'primary_place_id');
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

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_work')
            ->withTimestamps();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assetable_id')
            ->where('assetable_type', static::class);
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
