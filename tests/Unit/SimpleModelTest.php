<?php

use App\Models\Agent;
use App\Models\Place;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('work model can be instantiated', function () {
    $work = new Work([
        'title' => 'Test Work',
        'slug' => 'test-work',
        'type' => 'book',
        'status' => 'draft',
    ]);

    expect($work)->toBeInstanceOf(Work::class)
        ->and($work->title)->toBe('Test Work');
});

test('work model can be saved to database', function () {
    $work = Work::create([
        'title' => 'Test Work',
        'slug' => 'test-work-saved',
        'type' => 'book',
        'status' => 'draft',
    ]);

    expect($work)->toBeInstanceOf(Work::class)
        ->and($work->id)->not->toBeNull()
        ->and($work->title)->toBe('Test Work');

    $this->assertDatabaseHas('works', [
        'title' => 'Test Work',
        'slug' => 'test-work-saved',
    ]);
});

test('place model can be instantiated', function () {
    $place = new Place([
        'name' => 'Test Place',
        'slug' => 'test-place',
        'type' => 'city',
    ]);

    expect($place)->toBeInstanceOf(Place::class)
        ->and($place->name)->toBe('Test Place');
});

test('agent model can be instantiated', function () {
    $agent = new Agent([
        'name' => 'Test Agent',
        'type' => 'person',
    ]);

    expect($agent)->toBeInstanceOf(Agent::class)
        ->and($agent->name)->toBe('Test Agent');
});
