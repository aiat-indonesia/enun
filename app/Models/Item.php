<?php

namespace App\Models;

use App\Enums\ItemAvailability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'instance_id',
        'item_identifier',
        'location',
        'call_number',
        'availability',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'availability' => ItemAvailability::class,
            'metadata' => 'array',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assetable_id')
            ->where('assetable_type', static::class);
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
