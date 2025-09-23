<?php

use App\Models\Instance;
use App\Models\Item;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
});

describe('Work Media Collections', function () {
    it('has configured media collections', function () {
        $work = Work::factory()->create();

        expect($work->getMediaCollection('images'))->not->toBeNull()
            ->and($work->getMediaCollection('manuscripts'))->not->toBeNull()
            ->and($work->getMediaCollection('documents'))->not->toBeNull();
    });

    it('can add image files to images collection', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 800, 600);

        $media = $work->addMediaFromRequest('file')
            ->usingFileName('work-cover.jpg')
            ->toMediaCollection('images');

        expect($media->collection_name)->toBe('images')
            ->and($media->mime_type)->toBe('image/jpeg')
            ->and($work->getMedia('images'))->toHaveCount(1);
    });

    it('can add PDF files to manuscripts collection', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->create('manuscript.pdf', 1000, 'application/pdf');

        $media = $work->addMedia($file)
            ->usingFileName('manuscript-pages.pdf')
            ->toMediaCollection('manuscripts');

        expect($media->collection_name)->toBe('manuscripts')
            ->and($media->mime_type)->toBe('application/pdf')
            ->and($work->getMedia('manuscripts'))->toHaveCount(1);
    });

    it('can add document files to documents collection', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->create('research.pdf', 500, 'application/pdf');

        $media = $work->addMedia($file)
            ->usingFileName('research-notes.pdf')
            ->toMediaCollection('documents');

        expect($media->collection_name)->toBe('documents')
            ->and($media->mime_type)->toBe('application/pdf')
            ->and($work->getMedia('documents'))->toHaveCount(1);
    });

    it('can retrieve media by collection', function () {
        $work = Work::factory()->create();

        $work->addMedia(UploadedFile::fake()->image('image1.jpg'))
            ->toMediaCollection('images');
        $work->addMedia(UploadedFile::fake()->image('image2.jpg'))
            ->toMediaCollection('images');
        $work->addMedia(UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'))
            ->toMediaCollection('documents');

        expect($work->getMedia('images'))->toHaveCount(2)
            ->and($work->getMedia('documents'))->toHaveCount(1)
            ->and($work->getMedia('manuscripts'))->toHaveCount(0);
    });
});

describe('Instance Media Collections', function () {
    it('has configured media collections', function () {
        $instance = Instance::factory()->create();

        expect($instance->getMediaCollection('cover_images'))->not->toBeNull()
            ->and($instance->getMediaCollection('preview_pages'))->not->toBeNull()
            ->and($instance->getMediaCollection('documents'))->not->toBeNull();
    });

    it('can add cover images', function () {
        $instance = Instance::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg');

        $media = $instance->addMedia($file)
            ->toMediaCollection('cover_images');

        expect($media->collection_name)->toBe('cover_images')
            ->and($instance->getMedia('cover_images'))->toHaveCount(1);
    });

    it('can add preview pages', function () {
        $instance = Instance::factory()->create();
        $file = UploadedFile::fake()->create('preview.pdf', 200, 'application/pdf');

        $media = $instance->addMedia($file)
            ->toMediaCollection('preview_pages');

        expect($media->collection_name)->toBe('preview_pages')
            ->and($instance->getMedia('preview_pages'))->toHaveCount(1);
    });
});

describe('Item Media Collections', function () {
    it('has configured media collections', function () {
        $item = Item::factory()->create();

        expect($item->getMediaCollection('photos'))->not->toBeNull()
            ->and($item->getMediaCollection('scans'))->not->toBeNull()
            ->and($item->getMediaCollection('condition_reports'))->not->toBeNull();
    });

    it('can add item photos', function () {
        $item = Item::factory()->create();
        $file = UploadedFile::fake()->image('item-photo.jpg');

        $media = $item->addMedia($file)
            ->toMediaCollection('photos');

        expect($media->collection_name)->toBe('photos')
            ->and($item->getMedia('photos'))->toHaveCount(1);
    });

    it('can add digital scans', function () {
        $item = Item::factory()->create();
        $file = UploadedFile::fake()->create('scan.tiff', 5000, 'image/tiff');

        $media = $item->addMedia($file)
            ->toMediaCollection('scans');

        expect($media->collection_name)->toBe('scans')
            ->and($item->getMedia('scans'))->toHaveCount(1);
    });

    it('can add condition reports', function () {
        $item = Item::factory()->create();
        $file = UploadedFile::fake()->create('condition.pdf', 300, 'application/pdf');

        $media = $item->addMedia($file)
            ->toMediaCollection('condition_reports');

        expect($media->collection_name)->toBe('condition_reports')
            ->and($item->getMedia('condition_reports'))->toHaveCount(1);
    });
});

describe('Media File Operations', function () {
    it('can delete media files', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg');

        $media = $work->addMedia($file)->toMediaCollection('images');
        $mediaId = $media->id;

        expect($work->getMedia('images'))->toHaveCount(1);

        $media->delete();

        expect($work->fresh()->getMedia('images'))->toHaveCount(0);
    });

    it('can get media URLs', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg');

        $media = $work->addMedia($file)->toMediaCollection('images');

        expect($media->getUrl())->toContain('storage')
            ->and($media->getUrl())->toContain($media->file_name);
    });

    it('stores files in correct directory structure', function () {
        $work = Work::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg');

        $media = $work->addMedia($file)->toMediaCollection('images');

        expect($media->disk)->toBe('public')
            ->and($media->getPath())->toContain($work->id);
    });

    it('can handle multiple files in the same collection', function () {
        $work = Work::factory()->create();

        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg'),
            UploadedFile::fake()->image('image3.jpg'),
        ];

        foreach ($files as $file) {
            $work->addMedia($file)->toMediaCollection('images');
        }

        expect($work->getMedia('images'))->toHaveCount(3);
    });
});
