<?php

use App\Models\Agent;
use App\Models\Instance;
use App\Models\Place;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Work Model', function () {
    it('can be created with factory', function () {
        $work = Work::factory()->create();

        expect($work)->toBeInstanceOf(Work::class)
            ->and($work->title)->not->toBeEmpty()
            ->and($work->slug)->not->toBeEmpty()
            ->and($work->status)->toBe('draft');
    });

    it('has required fillable fields', function () {
        $work = Work::factory()->make([
            'title' => 'Test Tafsir Work',
            'slug' => 'test-tafsir-work',
            'summary' => ['A test summary'],
            'type' => 'tafsir',
            'status' => 'published',
        ]);

        expect($work->title)->toBe('Test Tafsir Work')
            ->and($work->slug)->toBe('test-tafsir-work')
            ->and($work->summary)->toBe(['A test summary'])
            ->and($work->type->value)->toBe('tafsir')
            ->and($work->status->value)->toBe('published');
    });

    it('handles JSON fields correctly', function () {
        $summary = ['First paragraph', 'Second paragraph'];
        $metadata = ['author' => 'Test Author', 'year' => 2024];
        $contributors = [
            ['name' => 'John Doe', 'role' => 'editor'],
            ['name' => 'Jane Smith', 'role' => 'translator'],
        ];

        $work = Work::factory()->create([
            'summary' => $summary,
            'metadata' => $metadata,
            'contributors' => $contributors,
        ]);

        expect($work->summary)->toBe($summary)
            ->and($work->metadata)->toBe($metadata)
            ->and($work->contributors)->toBe($contributors);
    });

    it('has soft deletes enabled', function () {
        $work = Work::factory()->create();
        $workId = $work->id;

        $work->delete();

        expect(Work::find($workId))->toBeNull()
            ->and(Work::withTrashed()->find($workId))->not->toBeNull();
    });

    it('generates unique slugs', function () {
        $work1 = Work::factory()->create(['title' => 'Same Title']);
        $work2 = Work::factory()->create(['title' => 'Same Title']);

        expect($work1->slug)->not->toBe($work2->slug);
    });
});

describe('Work Relationships', function () {
    it('belongs to a place', function () {
        $place = Place::factory()->create();
        $work = Work::factory()->create(['place_id' => $place->id]);

        expect($work->place)->toBeInstanceOf(Place::class)
            ->and($work->place->id)->toBe($place->id);
    });

    it('belongs to an author', function () {
        $author = Agent::factory()->create();
        $work = Work::factory()->create(['author_id' => $author->id]);

        expect($work->author)->toBeInstanceOf(Agent::class)
            ->and($work->author->id)->toBe($author->id);
    });

    it('has many instances', function () {
        $work = Work::factory()->create();
        $instances = Instance::factory(3)->create(['work_id' => $work->id]);

        expect($work->instances)->toHaveCount(3)
            ->and($work->instances->first())->toBeInstanceOf(Instance::class);
    });

    it('has many agents through polymorphic relationship', function () {
        $work = Work::factory()->create();
        $agents = Agent::factory(2)->create();

        $work->agents()->attach($agents[0]->id, ['role' => 'author']);
        $work->agents()->attach($agents[1]->id, ['role' => 'translator']);

        expect($work->agents)->toHaveCount(2)
            ->and($work->agents->first())->toBeInstanceOf(Agent::class);
    });

    it('can access agent roles through pivot', function () {
        $work = Work::factory()->create();
        $agent = Agent::factory()->create();

        $work->agents()->attach($agent->id, ['role' => 'author']);

        $attachedAgent = $work->agents->first();
        expect($attachedAgent->pivot->role)->toBe('author');
    });

    it('has media collections configured', function () {
        $work = Work::factory()->create();

        expect($work->getMediaCollection('images'))->not->toBeNull()
            ->and($work->getMediaCollection('manuscripts'))->not->toBeNull()
            ->and($work->getMediaCollection('documents'))->not->toBeNull();
    });
});

describe('Work Status', function () {
    it('defaults to draft status', function () {
        $work = Work::factory()->create();

        expect($work->status)->toBe('draft');
    });

    it('can transition between statuses', function () {
        $work = Work::factory()->create(['status' => 'draft']);

        $work->update(['status' => 'in_review']);
        expect($work->fresh()->status)->toBe('in_review');

        $work->update(['status' => 'published']);
        expect($work->fresh()->status)->toBe('published');

        $work->update(['status' => 'archived']);
        expect($work->fresh()->status)->toBe('archived');
    });

    it('can filter by status', function () {
        Work::factory()->create(['status' => 'draft']);
        Work::factory()->create(['status' => 'published']);
        Work::factory()->create(['status' => 'archived']);

        expect(Work::where('status', 'draft')->count())->toBe(1)
            ->and(Work::where('status', 'published')->count())->toBe(1)
            ->and(Work::where('status', 'archived')->count())->toBe(1);
    });
});

describe('Work Scopes and Queries', function () {
    it('can be filtered by type', function () {
        Work::factory()->create(['type' => 'manuscript']);
        Work::factory()->create(['type' => 'tafsir']);
        Work::factory()->create(['type' => 'book']);

        expect(Work::where('type', 'manuscript')->count())->toBe(1)
            ->and(Work::where('type', 'tafsir')->count())->toBe(1)
            ->and(Work::where('type', 'book')->count())->toBe(1);
    });

    it('can search by title', function () {
        Work::factory()->create(['title' => 'Tafsir Al-Qurtubi']);
        Work::factory()->create(['title' => 'Tafsir Ibn Kathir']);
        Work::factory()->create(['title' => 'Sahih Bukhari']);

        $tafsirWorks = Work::where('title', 'LIKE', '%Tafsir%')->get();

        expect($tafsirWorks)->toHaveCount(2);
    });

    it('can be ordered by creation date', function () {
        $oldWork = Work::factory()->create(['created_at' => now()->subDays(2)]);
        $newWork = Work::factory()->create(['created_at' => now()]);

        $orderedWorks = Work::orderBy('created_at', 'desc')->get();

        expect($orderedWorks->first()->id)->toBe($newWork->id)
            ->and($orderedWorks->last()->id)->toBe($oldWork->id);
    });
});
