<?php

use App\Filament\Resources\Places\Pages\CreatePlace;
use App\Filament\Resources\Places\Pages\EditPlace;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render place create form with map picker', function () {
    Livewire::test(CreatePlace::class)
        ->assertFormExists()
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('type')
        ->assertFormFieldExists('coordinates');
});

it('can create place with coordinates from map picker', function () {
    $placeData = [
        'name' => 'Test Place',
        'type' => 'city',
        'coordinates' => [
            'lat' => -6.2088,
            'lng' => 106.8456,
        ],
        'parent_id' => null,
    ];

    Livewire::test(CreatePlace::class)
        ->fillForm($placeData)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('places', [
        'name' => 'Test Place',
        'type' => 'city',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);
});

it('can edit place and update coordinates', function () {
    $place = Place::factory()->create([
        'name' => 'Original Place',
        'type' => 'city',
        'lat' => -6.2088,
        'lng' => 106.8456,
    ]);

    $updatedData = [
        'name' => 'Updated Place',
        'type' => 'regency',
        'coordinates' => [
            'lat' => -7.2575,
            'lng' => 112.7521, // Surabaya
        ],
    ];

    Livewire::test(EditPlace::class, [
        'record' => $place->getRouteKey(),
    ])
        ->fillForm($updatedData)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($place->fresh())
        ->name->toBe('Updated Place')
        ->type->toBe('regency')
        ->lat->toBe(-7.2575)
        ->lng->toBe(112.7521);
});

it('validates required fields on place form', function () {
    Livewire::test(CreatePlace::class)
        ->fillForm([
            'name' => '',
            'type' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name', 'type']);
});

it('coordinates are optional for place creation', function () {
    $placeData = [
        'name' => 'Test Place Without Coordinates',
        'type' => 'city',
    ];

    Livewire::test(CreatePlace::class)
        ->fillForm($placeData)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('places', [
        'name' => 'Test Place Without Coordinates',
        'type' => 'city',
    ]);
});
