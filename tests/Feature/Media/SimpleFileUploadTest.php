<?php

use App\Models\Instance;
use App\Models\Item;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

describe('Media Collections Configuration', function () {
    it('Work has configured media collections', function () {
        $work = Work::factory()->create();

        expect($work->getMediaCollection('images'))->not->toBeNull()
            ->and($work->getMediaCollection('manuscripts'))->not->toBeNull()
            ->and($work->getMediaCollection('documents'))->not->toBeNull();
    });

    it('Instance has configured media collections', function () {
        $work = Work::factory()->create();
        $instance = Instance::factory()->for($work)->create();

        expect($instance->getMediaCollection('cover_images'))->not->toBeNull()
            ->and($instance->getMediaCollection('preview_pages'))->not->toBeNull()
            ->and($instance->getMediaCollection('documents'))->not->toBeNull();
    });

    it('Item has configured media collections', function () {
        $work = Work::factory()->create();
        $instance = Instance::factory()->for($work)->create();
        $item = Item::factory()->for($instance)->create();

        expect($item->getMediaCollection('photos'))->not->toBeNull()
            ->and($item->getMediaCollection('scans'))->not->toBeNull()
            ->and($item->getMediaCollection('condition_reports'))->not->toBeNull();
    });
});

describe('Media Collections Behavior', function () {
    it('starts with empty media collections', function () {
        $work = Work::factory()->create();

        expect($work->getMedia('images'))->toHaveCount(0)
            ->and($work->getMedia('manuscripts'))->toHaveCount(0)
            ->and($work->getMedia('documents'))->toHaveCount(0);
    });

    it('can get media collections count', function () {
        $work = Work::factory()->create();

        // Test that Work has 3 media collections
        $workCollections = $work->getRegisteredMediaCollections();
        expect($workCollections)->toHaveCount(3);

        $work = Work::factory()->create();
        $instance = Instance::factory()->for($work)->create();

        // Test that Instance has 3 media collections
        $instanceCollections = $instance->getRegisteredMediaCollections();
        expect($instanceCollections)->toHaveCount(3);

        $item = Item::factory()->for($instance)->create();

        // Test that Item has 3 media collections
        $itemCollections = $item->getRegisteredMediaCollections();
        expect($itemCollections)->toHaveCount(3);
    });
});

describe('Model Relationships with Media', function () {
    it('Work can access Instance media through relationships', function () {
        $work = Work::factory()->create();
        $instance = Instance::factory()->for($work)->create();

        expect($work->instances)->toHaveCount(1)
            ->and($work->instances->first()->getRegisteredMediaCollections())->toHaveCount(3);
    });

    it('Instance can access Item media through relationships', function () {
        $work = Work::factory()->create();
        $instance = Instance::factory()->for($work)->create();
        $item = Item::factory()->for($instance)->create();

        expect($instance->items)->toHaveCount(1)
            ->and($instance->items->first()->getRegisteredMediaCollections())->toHaveCount(3);
    });
});
