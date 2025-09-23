<?php

use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Basic Filament Integration', function () {
    it('can create a user for testing', function () {
        $user = User::factory()->create();

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->email)->not->toBeEmpty();
    });

    it('can authenticate a user', function () {
        $user = User::factory()->create();

        $this->actingAs($user);

        expect($this->app['auth']->check())->toBeTrue();
    });

    it('can create works for testing', function () {
        $works = Work::factory(5)->create();

        expect($works)->toHaveCount(5)
            ->and(Work::count())->toBe(5);
    });
});
