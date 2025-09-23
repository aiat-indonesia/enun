<?php

namespace App\Models;

use App\Enums\WorkStatus;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Work extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\WorkFactory> */
    use HasFactory, HasTags, InteractsWithMedia, Searchable, SoftDeletes;

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
            'type' => WorkType::class,
            'status' => WorkStatus::class,
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // Only include actual database columns for Scout search
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'summary' => $this->summary,
            'type' => $this->type,
            'status' => $this->status,
            'languages' => $this->languages ? implode(' ', $this->languages) : '',
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'published';
    }
}
