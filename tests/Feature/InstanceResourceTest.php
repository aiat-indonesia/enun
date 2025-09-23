<?php

use App\Models\Agent;
use App\Models\Instance;
use App\Models\Place;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can create an instance with relationships', function () {
    $work = Work::factory()->create(['title' => 'Test Work']);
    $publisher = Agent::factory()->create(['name' => 'Test Publisher']);
    $place = Place::factory()->create(['name' => 'Jakarta']);

    $instance = Instance::create([
        'work_id' => $work->id,
        'label' => 'First Edition',
        'publisher_id' => $publisher->id,
        'publication_place_id' => $place->id,
        'publication_year' => 2023,
        'format' => 'print',
    ]);

    expect($instance->work_id)->toBe($work->id);
    expect($instance->publisher_id)->toBe($publisher->id);
    expect($instance->publication_place_id)->toBe($place->id);
    expect($instance->label)->toBe('First Edition');
});

it('can create instance with identifiers array', function () {
    $work = Work::factory()->create();

    $instance = Instance::factory()->create([
        'work_id' => $work->id,
        'identifiers' => [
            [
                'type' => 'isbn',
                'value' => '978-0123456789',
                'note' => 'Original ISBN',
            ],
            [
                'type' => 'barcode',
                'value' => '123456789012',
            ],
        ],
    ]);

    expect($instance->identifiers)->toHaveCount(2);
    expect($instance->identifiers[0]['type'])->toBe('isbn');
    expect($instance->identifiers[1]['type'])->toBe('barcode');
});

it('can search instances by work title relationship', function () {
    $work1 = Work::factory()->create(['title' => 'Tafsir Al-Quran']);
    $work2 = Work::factory()->create(['title' => 'Hadith Collection']);

    $instance1 = Instance::factory()->create([
        'work_id' => $work1->id,
        'label' => 'First Edition',
    ]);

    $instance2 = Instance::factory()->create([
        'work_id' => $work2->id,
        'label' => 'Second Edition',
    ]);

    $tafsirInstances = Instance::whereHas('work', function ($query) {
        $query->where('title', 'like', '%Tafsir%');
    })->get();

    expect($tafsirInstances)->toHaveCount(1);
    expect($tafsirInstances->first()->id)->toBe($instance1->id);
});

it('requires work_id and label for instance creation', function () {
    $instance = new Instance;
    $instance->label = '';

    expect(fn () => $instance->save())->toThrow(Exception::class);
});

it('can update instance with metadata', function () {
    $instance = Instance::factory()->create();

    $instance->update([
        'metadata' => [
            'pages' => 500,
            'dimensions' => '21x14 cm',
            'weight' => '800g',
        ],
    ]);

    $instance->refresh();
    expect($instance->metadata['pages'])->toBe(500);
    expect($instance->metadata['dimensions'])->toBe('21x14 cm');
});
