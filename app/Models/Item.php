<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'instance_id',
        'identifier',
        'location',
        'condition',
        'current_holder',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'location');
    }

    public function currentHolder(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'current_holder');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useDisk('public');

        $this->addMediaCollection('scans')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/tiff', 'application/pdf'])
            ->useDisk('public');

        $this->addMediaCollection('condition_reports')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png'])
            ->useDisk('public');
    }
}
