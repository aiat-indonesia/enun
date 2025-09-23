<?php

use App\Models\Place;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a place for primary_place relationship
    $this->place = Place::factory()->create([
        'name' => 'Jakarta',
    ]);
});

it('can search works using scout search', function () {
    // Create test works
    $work1 = Work::factory()->create([
        'title' => 'Introduction to Laravel',
        'subtitle' => 'Modern PHP Framework',
        'summary' => 'A comprehensive guide to Laravel development',
        'status' => 'published',
        'type' => 'book',
        'primary_place_id' => $this->place->id,
    ]);

    $work2 = Work::factory()->create([
        'title' => 'PHP Programming',
        'subtitle' => 'Advanced Techniques',
        'summary' => 'Deep dive into PHP programming concepts',
        'status' => 'published',
        'type' => 'article',
        'primary_place_id' => $this->place->id,
    ]);

    $work3 = Work::factory()->create([
        'title' => 'JavaScript Fundamentals',
        'subtitle' => 'Frontend Development',
        'summary' => 'Learn JavaScript from scratch',
        'status' => 'draft',
        'type' => 'manuscript',
        'primary_place_id' => $this->place->id,
    ]);

    // Test Scout search
    $response = $this->getJson('/api/v1/works?q=Laravel');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'subtitle',
                    'summary',
                    'slug',
                    'type',
                    'status',
                    'languages',
                    'primary_place_id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('Introduction to Laravel');
});

it('can search works using legacy search', function () {
    // Create test works
    $work1 = Work::factory()->create([
        'title' => 'PHP Best Practices',
        'subtitle' => 'Clean Code',
        'summary' => 'Writing maintainable PHP code',
        'status' => 'published',
        'type' => 'book',
        'primary_place_id' => $this->place->id,
    ]);

    $work2 = Work::factory()->create([
        'title' => 'Laravel Testing',
        'subtitle' => 'Unit and Feature Tests',
        'summary' => 'Complete testing guide for Laravel',
        'status' => 'published',
        'type' => 'article',
        'primary_place_id' => $this->place->id,
    ]);

    // Test legacy search
    $response = $this->getJson('/api/v1/works?search=PHP');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('PHP Best Practices');
});

it('can filter works by type', function () {
    // Create test works
    Work::factory()->create([
        'title' => 'Book Title',
        'type' => 'book',
        'status' => 'published',
        'primary_place_id' => $this->place->id,
    ]);

    Work::factory()->create([
        'title' => 'Article Title',
        'type' => 'article',
        'status' => 'published',
        'primary_place_id' => $this->place->id,
    ]);

    // Test type filter
    $response = $this->getJson('/api/v1/works?type=book');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['type'])->toBe('book');
});

it('can filter works by status', function () {
    // Create test works
    Work::factory()->create([
        'title' => 'Published Work',
        'status' => 'published',
        'type' => 'book',
        'primary_place_id' => $this->place->id,
    ]);

    Work::factory()->create([
        'title' => 'Draft Work',
        'status' => 'draft',
        'type' => 'article',
        'primary_place_id' => $this->place->id,
    ]);

    // Test status filter
    $response = $this->getJson('/api/v1/works?status=published');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['status'])->toBe('published');
});

it('can combine search and filters', function () {
    // Create test works
    Work::factory()->create([
        'title' => 'Laravel Advanced',
        'type' => 'book',
        'status' => 'published',
        'primary_place_id' => $this->place->id,
    ]);

    Work::factory()->create([
        'title' => 'Laravel Basics',
        'type' => 'article',
        'status' => 'draft',
        'primary_place_id' => $this->place->id,
    ]);

    // Test combined search and filter
    $response = $this->getJson('/api/v1/works?q=Laravel&type=book&status=published');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('Laravel Advanced');
    expect($data[0]['type'])->toBe('book');
    expect($data[0]['status'])->toBe('published');
});

it('can sort works by different fields', function () {
    // Create test works with different dates
    $work1 = Work::factory()->create([
        'title' => 'First Work',
        'created_at' => now()->subDays(2),
        'primary_place_id' => $this->place->id,
    ]);

    $work2 = Work::factory()->create([
        'title' => 'Second Work',
        'created_at' => now()->subDay(),
        'primary_place_id' => $this->place->id,
    ]);

    // Test sorting by created_at ascending
    $response = $this->getJson('/api/v1/works?sort=created_at&direction=asc');

    $response->assertSuccessful();

    $data = $response->json('data');
    expect($data[0]['title'])->toBe('First Work');
    expect($data[1]['title'])->toBe('Second Work');
});

it('returns paginated results', function () {
    // Create multiple works
    Work::factory()->count(25)->create([
        'primary_place_id' => $this->place->id,
    ]);

    $response = $this->getJson('/api/v1/works?per_page=10');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ],
        ]);

    $meta = $response->json('meta');
    expect($meta['per_page'])->toBe(10);
    expect($meta['total'])->toBe(25);
});
