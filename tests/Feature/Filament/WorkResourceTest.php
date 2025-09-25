<?php

use App\Filament\Resources\Works\Pages\CreateWork;
use App\Filament\Resources\Works\Pages\EditWork;
use App\Filament\Resources\Works\Pages\ListWorks;
use App\Models\Place;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create super admin role
    $role = Role::create(['name' => 'super_admin']);

    // Create essential permissions manually
    $permissions = [
        'ViewAny:Work',
        'View:Work',
        'Create:Work',
        'Update:Work',
        'Delete:Work',
        'ViewAny:Agent',
        'View:Agent',
        'Create:Agent',
        'Update:Agent',
        'Delete:Agent',
        'ViewAny:Place',
        'View:Place',
        'Create:Place',
        'Update:Place',
        'Delete:Place',
        'ViewAny:Instance',
        'View:Instance',
        'Create:Instance',
        'Update:Instance',
        'Delete:Instance',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }

    // Assign all permissions to super admin role
    $role->givePermissionTo($permissions);

    // Create user and assign super admin role
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user);
});

describe('Work Resource List', function () {
    it('can render the list page', function () {
        Livewire::test(ListWorks::class)
            ->assertSuccessful();
    });

    it('can list works in table', function () {
        $works = Work::factory(3)->create();

        Livewire::test(ListWorks::class)
            ->assertCanSeeTableRecords($works);
    });

    it('can search works by title', function () {
        $work1 = Work::factory()->create(['title' => 'Tafsir Al-Qurtubi']);
        $work2 = Work::factory()->create(['title' => 'Sahih Bukhari']);

        Livewire::test(ListWorks::class)
            ->searchTable('Tafsir')
            ->assertCanSeeTableRecords([$work1])
            ->assertCanNotSeeTableRecords([$work2]);
    });

    it('can filter works by status', function () {
        $draftWork = Work::factory()->create(['status' => 'draft']);
        $publishedWork = Work::factory()->create(['status' => 'published']);

        Livewire::test(ListWorks::class)
            ->filterTable('status', 'draft')
            ->assertCanSeeTableRecords([$draftWork])
            ->assertCanNotSeeTableRecords([$publishedWork]);
    });

    it('can filter works by type', function () {
        $manuscript = Work::factory()->create(['type' => 'manuscript']);
        $tafsir = Work::factory()->create(['type' => 'tafsir']);

        Livewire::test(ListWorks::class)
            ->filterTable('type', 'manuscript')
            ->assertCanSeeTableRecords([$manuscript])
            ->assertCanNotSeeTableRecords([$tafsir]);
    });

    it('can sort works by creation date', function () {
        $oldWork = Work::factory()->create(['created_at' => now()->subDays(2)]);
        $newWork = Work::factory()->create(['created_at' => now()]);

        Livewire::test(ListWorks::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords([$newWork, $oldWork], inOrder: true);
    });
});

describe('Work Resource Create', function () {
    it('can render the create page', function () {
        Livewire::test(CreateWork::class)
            ->assertSuccessful();
    });

    it('can create a work', function () {
        $place = Place::factory()->create();

        Livewire::test(CreateWork::class)
            ->fillForm([
                'title' => 'Test Tafsir',
                'slug' => 'test-tafsir',
                'summary' => 'A test summary',
                'type' => 'tafsir',
                'status' => 'draft',
                'place_id' => $place->id,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas('works', [
            'title' => 'Test Tafsir',
            'slug' => 'test-tafsir',
            'type' => 'tafsir',
            'status' => 'draft',
        ]);
    });

    it('validates required fields', function () {
        Livewire::test(CreateWork::class)
            ->fillForm([
                'summary' => 'A test summary',
            ])
            ->call('create')
            ->assertHasFormErrors(['title', 'slug']);
    });

    it('validates unique slug', function () {
        Work::factory()->create(['slug' => 'existing-slug']);

        Livewire::test(CreateWork::class)
            ->fillForm([
                'title' => 'New Work',
                'slug' => 'existing-slug',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    });

    it('can handle JSON fields', function () {
        $place = Place::factory()->create();

        Livewire::test(CreateWork::class)
            ->fillForm([
                'title' => 'Test Work with Metadata',
                'slug' => 'test-work-metadata',
                'summary' => 'Test summary',
                'type' => 'manuscript',
                'status' => 'draft',
                'place_id' => $place->id,
                'metadata' => ['key1' => 'value1', 'key2' => 'value2'],
            ])
            ->call('create')
            ->assertNotified();

        $work = Work::where('slug', 'test-work-metadata')->first();
        expect($work->metadata)->toHaveKey('key1')
            ->and($work->metadata)->toBe(['key1' => 'value1', 'key2' => 'value2']);
    });
});

describe('Work Resource Edit', function () {
    it('can render the edit page', function () {
        $work = Work::factory()->create();

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->assertSuccessful();
    });

    it('can retrieve existing data', function () {
        $place = Place::factory()->create();
        $work = Work::factory()->create([
            'title' => 'Existing Work',
            'type' => 'manuscript',
            'place_id' => $place->id,
        ]);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->assertFormSet([
                'title' => 'Existing Work',
                'type' => 'manuscript',
                'place_id' => $place->id,
            ]);
    });

    it('can update a work', function () {
        $work = Work::factory()->create(['title' => 'Original Title']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->fillForm([
                'title' => 'Updated Title',
                'summary' => 'Updated summary',
            ])
            ->call('save')
            ->assertNotified();

        expect($work->fresh()->title)->toBe('Updated Title')
            ->and($work->fresh()->summary)->toBe('Updated summary');
    });

    it('can delete a work', function () {
        $work = Work::factory()->create();

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('delete');

        $this->assertSoftDeleted('works', ['id' => $work->id]);
    });
});

describe('Work Resource Workflow Actions', function () {
    it('can submit work for review', function () {
        $work = Work::factory()->create(['status' => 'draft']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('submit_for_review');

        expect($work->fresh()->status)->toBe(\App\Enums\WorkStatus::InReview);
    });

    it('can approve and publish work', function () {
        $work = Work::factory()->create(['status' => 'in_review']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('approve');

        expect($work->fresh()->status)->toBe(\App\Enums\WorkStatus::Published);
    });

    it('can reject work back to draft', function () {
        $work = Work::factory()->create(['status' => 'in_review']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('reject');

        expect($work->fresh()->status)->toBe(\App\Enums\WorkStatus::Draft);
    });

    it('can archive published work', function () {
        $work = Work::factory()->create(['status' => 'published']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('archive');

        expect($work->fresh()->status)->toBe(\App\Enums\WorkStatus::Archived);
    });

    it('can restore archived work to published', function () {
        $work = Work::factory()->create(['status' => 'archived']);

        Livewire::test(EditWork::class, ['record' => $work->id])
            ->callAction('restore_to_published');

        expect($work->fresh()->status)->toBe(\App\Enums\WorkStatus::Published);
    });
});
