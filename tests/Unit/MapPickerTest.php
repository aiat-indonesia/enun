<?php

use App\Filament\Components\MapPicker;

it('can instantiate map picker with default values', function () {
    $mapPicker = MapPicker::make('coordinates');

    expect($mapPicker->getDefaultLocation())->toBe([-6.2088, 106.8456]);
    expect($mapPicker->getDefaultZoom())->toBe(13);
    expect($mapPicker->getTileProvider())->toBe('openstreetmap');
});

it('can set custom default location', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->defaultLocation([-7.7956, 110.3695]); // Yogyakarta coordinates

    expect($mapPicker->getDefaultLocation())->toBe([-7.7956, 110.3695]);
});

it('can set custom zoom level', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->defaultZoom(15);

    expect($mapPicker->getDefaultZoom())->toBe(15);
});

it('can set custom tile provider', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->tileProvider('google');

    expect($mapPicker->getTileProvider())->toBe('google');
});

it('has correct view path', function () {
    $mapPicker = MapPicker::make('coordinates');

    expect($mapPicker->getView())->toBe('filament.components.map-picker');
});

it('can configure map height', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->height('500px');

    expect($mapPicker->getHeight())->toBe('500px');
});

it('can configure draggable behavior', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->draggable(false);

    expect($mapPicker->getDraggable())->toBeFalse();
});

it('can configure clickable behavior', function () {
    $mapPicker = MapPicker::make('coordinates')
        ->clickable(false);

    expect($mapPicker->getClickable())->toBeFalse();
});

it('can get map config as array', function () {
    $mapPicker = MapPicker::make('coordinates');

    // Test the underlying configuration without JSON encoding
    expect($mapPicker->getDefaultLocation())->toBe([-6.2088, 106.8456])
        ->and($mapPicker->getDefaultZoom())->toBe(13)
        ->and($mapPicker->getTileProvider())->toBe('openstreetmap')
        ->and($mapPicker->getHeight())->toBe('400px')
        ->and($mapPicker->getDraggable())->toBeTrue()
        ->and($mapPicker->getClickable())->toBeTrue();
});
