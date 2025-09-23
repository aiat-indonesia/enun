<?php

use App\Filament\Resources\Places\Pages\CreatePlace;
use App\Filament\Resources\Places\Pages\EditPlace;
use App\Models\Place;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup for each test
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

it('can create a place with coordinates', function () {
    $placeData = [
        'name' => 'Test Place',
        'type' => 'city',
        'coordinates' => [
            'lat' => -6.2088,
            'lng' => 106.8456,
        ],
    ];

    Livewire::test(CreatePlace::class)
        ->fillForm($placeData)
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $place = Place::where('name', 'Test Place')->first();

    expect($place)->not->toBeNull();
    expect($place->lat)->toBe(-6.2088);
    expect($place->lng)->toBe(106.8456);
    expect($place->type)->toBe('city');
});

it('can edit a place and update coordinates', function () {
    $place = Place::factory()->create([
        'name' => 'Original Place',
        'type' => 'city',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);

    $updatedData = [
        'name' => 'Updated Place',
        'type' => 'province',
        'coordinates' => [
            'lat' => -7.2575,
            'lng' => 112.7521, // Surabaya coordinates
        ],
    ];

    Livewire::test(EditPlace::class, [
        'record' => $place->getRouteKey(),
    ])
        ->fillForm($updatedData)
        ->call('save')
        ->assertNotified();

    $place->refresh();

    expect($place->name)->toBe('Updated Place');
    expect($place->type)->toBe('province');
    expect($place->lat)->toBe(-7.2575);
    expect($place->lng)->toBe(112.7521);
});

it('loads existing coordinates when editing a place', function () {
    $place = Place::factory()->create([
        'name' => 'Test Place',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);

    $component = Livewire::test(EditPlace::class, [
        'record' => $place->getRouteKey(),
    ]);

    // Check that the form is populated with existing data
    $component->assertFormSet([
        'name' => 'Test Place',
        'lat' => -6.2088,
        'lng' => 106.8456,
        'coordinates' => [
            'lat' => -6.2088,
            'lng' => 106.8456,
        ],
    ]);
});

it('can handle places without coordinates', function () {
    $placeData = [
        'name' => 'Place Without Coordinates',
        'type' => 'city',
        // No coordinates provided
    ];

    Livewire::test(CreatePlace::class)
        ->fillForm($placeData)
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $place = Place::where('name', 'Place Without Coordinates')->first();

    expect($place)->not->toBeNull();
    expect($place->lat)->toBeNull();
    expect($place->lng)->toBeNull();
});
