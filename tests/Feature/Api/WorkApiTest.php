<?php

use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Work API', function () {
    it('allows public access to works list', function () {
        Work::factory(3)->create();

        $response = $this->getJson('/api/v1/works');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(3);
    });

    it('returns paginated works', function () {
        Work::factory(15)->create();

        $response = $this->getJson('/api/v1/works');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'type',
                        'summary',
                        'languages',
                    ],
                ],
                'links',
                'meta',
            ]);

        expect($response->json('meta.total'))->toBe(15);
    });

    it('filters works by type', function () {
        Work::factory(3)->create(['type' => 'manuscript']);
        Work::factory(2)->create(['type' => 'book']);

        $response = $this->getJson('/api/v1/works?type=manuscript');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(3);
    });

    it('returns single work details', function () {
        $work = Work::factory()->create();

        $response = $this->getJson("/api/v1/works/{$work->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $work->id,
                    'title' => $work->title,
                    'slug' => $work->slug,
                ],
            ]);
    });

    it('returns 404 for non-existent work', function () {
        $response = $this->getJson('/api/v1/works/999');

        $response->assertNotFound();
    });

    it('handles invalid parameters gracefully', function () {
        Work::factory(5)->create();

        $response = $this->getJson('/api/v1/works?invalid_param=test');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(5);
    });
});
