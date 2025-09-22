<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Instance extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\InstanceFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'work_id',
        'label',
        'publisher_id',
        'publication_place_id',
        'publication_year',
        'format',
        'identifiers',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'identifiers' => 'array',
            'metadata' => 'array',
        ];
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'publisher_id');
    }

    public function publicationPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'publication_place_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function agents(): MorphToMany
    {
        return $this->morphToMany(Agent::class, 'agentable', 'agent_role')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assetable_id')
            ->where('assetable_type', static::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('preview_pages')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/tiff', 'application/pdf'])
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->useDisk('public');
    }
}
