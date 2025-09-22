---
applyTo: "**"
---

title: e-nun Project Instructions

# e-nun — Project Instructions

This document summarizes the technical plan, data model, conventions, and implementation steps for the e-nun application (a repository of Qur'anic works and Nusantara tafsir). It is meant to help new contributors quickly understand the project's goals, constraints, and design decisions.

Key notes:

-   Single management application: Laravel + Filament (admin).
-   Public frontend: Nuxt (map & public browsing) — the frontend is out of scope for this repository but API endpoints will be provided.
-   Database: MySQL (InnoDB). Use utf8mb4 charset and `utf8mb4_unicode_ci` collation.
-   Data will be entered manually via Filament; an automated import pipeline is not required for the MVP.

## Goals (MVP)

-   Provide a central catalog of Qur'anic manuscripts, tafsir, books, journals, and related works from the Nusantara region.
-   Store rich metadata (agents/authors, editions/instances, copies/items, places, references, assets/files, marketplace links).
-   Provide GeoJSON endpoints for mapping on the Nuxt frontend and public API endpoints for listing and detail pages.
-   Admin management via Filament: CRUD, review/publish workflow, and asset uploads.

## Database and MySQL considerations

-   Use a recent MySQL (8.x) with InnoDB.
-   For geolocation you may use MySQL `POINT` (with spatial index) for spatial queries, or store `lat`/`lng` as DECIMAL(10,7) for a simpler setup.
-   Use JSON columns where appropriate (MySQL 5.7+), but be aware that JSON query/indexing capabilities in MySQL are more limited than Postgres `jsonb`.
-   Full-text search is available in MySQL (InnoDB fulltext), but Meilisearch or Elasticsearch provide better ranking and fuzzy search for production.

## Data model (high-level)

We will use a simple Work → Instance → Item structure for conceptual clarity, but keep the schema generic and practical for manual entry.

-   works

    -   Represents the conceptual work (title, languages, subjects, conceptual authors)
    -   Key columns: id, slug, title, subtitle, languages (JSON), summary, type, status, primary_place_id, metadata (JSON)

-   instances

    -   Represents physical/published embodiments of a Work (publisher, place, year, format)
    -   Key columns: id, work_id, label, publisher_id, publication_place_id, publication_year, format, identifiers (JSON), metadata (JSON)

-   items
    -   Represents individual copies (physical or electronic) of an Instance (shelfmark, barcode, location)
    -   Key columns: id, instance_id, item_identifier, location, call_number, availability, metadata (JSON)

Supporting tables (normalized):

-   agents — people/organizations; a polymorphic pivot `agent_role` associates agents to works or instances with a role (author, editor, translator, publisher, etc.).
-   places — hierarchical places (province, regency, city) with centroid lat/lng and optional GeoJSON polygon.
-   subjects/tags — topical subjects and genres. We will use Spatie's Tags package with a Filament integration to provide an ergonomic tagging UI; when structured relations are needed we will use a pivot table like `subject_work`.
-   assets — file records (linked to work/instance/item); store disk, path, mime, size, and extracted_text for indexing.

Design notes:

-   Normalize entities that are frequently queried or aggregated: agents, places, tags, assets.
-   Use JSON columns for flexible, read-only, or rarely-queried data (alternative titles, external identifiers arrays, raw payloads), but avoid using JSON as a replacement for relations you will need to filter or join.

## Mapping notes (Bibframe adaptation)

We will not adopt Bibframe-specific column names or a strict Bibframe schema. Bibframe was used earlier as a conceptual reference only. For e-nun we will keep the schema practical and generic while preserving the Work → Instance → Item mental model:

-   Work-level: store title, language(s), summary, and relations to agents and subjects (tags).
-   Instance-level: store publisher, publication place/year, format, and identifiers (ISBN/DOI) in structured columns or JSON.
-   Item-level: store copy-level data (barcode, shelfmark, physical/virtual location) where relevant.

This keeps the model simple and suitable for manual entry via Filament while remaining flexible enough to support future import mapping or transformations if needed.

## Filament resources & admin UX

-   WorkResource
    -   Tabs: Metadata (title, languages, summary), Agents (relationship manager), Instances (inline relation manager), Subjects (tag field), Assets (relation manager), Notes/Raw metadata (JSON repeater)
-   InstanceResource (can be embedded in the Work form or exist as a standalone resource)
-   ItemResource
-   AgentResource, PlaceResource, SubjectResource, AssetResource
-   Workflow: status field (draft → review → published). Provide a Review queue page/action in Filament.

### Leaflet admin map picker (Filament)

-   For admin map interactions we recommend using LeafletJS. There is a community package (afsakar/filament-leaflet-map-picker) which targets Filament v3; for this project we will fork/port or implement a small local Form Component (`app/Filament/Components/MapPicker`) compatible with Filament v4 and Livewire v3. Key notes:
    -   Copy the minimal JS/CSS assets (or reference Leaflet from CDN) and include them in the component Blade view.
    -   The component should emit/set form state as a JSON or array with `lat` and `lng` (or two separate fields) so it integrates cleanly with Filament forms.
    -   Keep the component lightweight: store coordinates as DECIMAL(10,7) on `places` and return GeoJSON from API endpoints; add optional support later for polygons or GeoJSON uploads.

## Public API for the Nuxt frontend

-   GET /api/v1/works — list (filters: type, year, place, author, tag, q)
-   GET /api/v1/works/{id} — detail (includes assets, instances, related works)
-   GET /api/v1/instances/{id} — instance detail
-   GET /api/v1/places — list of places with centroid
-   GET /api/v1/geo/points?level=province — GeoJSON FeatureCollection (properties: place_id, name, count, sample_work_ids)

Note about GeoJSON: API endpoints that return spatial data should return a valid GeoJSON FeatureCollection. Each Feature's geometry can be a Point (from stored `lat`/`lng`) and properties should include identifiers and display values (for example: `place_id`, `name`, `count`, `sample_work_ids`). Keep geometries simple for the Nuxt map and let the frontend handle clustering and tile providers.

-   GET /api/v1/works/{id}/assets/{asset_id}/download — signed URL (if private)

## Geospatial strategy (MySQL-specific)

-   Option A (simpler): store `lat` and `lng` as DECIMAL(10,7) on `places` (and optionally `works`) and compute distances in application code.
-   Option B (spatial): use MySQL `POINT` with spatial indexes (MySQL 8.x) if you need more advanced spatial queries.
-   For the Nuxt map, the backend should return GeoJSON; convert from POINT or lat/lng as needed.

## Storage & assets

-   Use Laravel filesystem disks (local for development). Store disk and path in the `assets` table.
-   Use signed URLs for private assets.

Quick install (development):

-   Spatie Media Library (basic):

    1. Install: `composer require spatie/laravel-medialibrary`
    2. Publish config & migrations: `php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"` then run `php artisan migrate`.
    3. Configure collections in your `Asset` model and register disk(s) in `config/filesystems.php`.

Keep media collections minimal for the MVP (e.g., `images`, `pdfs`, `manuscripts`) and add extra conversions/optimizations later.

## Auth, roles & workflow

-   Roles: admin, editor, reviewer, contributor.
-   Permissions: use Filament Shield for role and permission management. Filament Shield integrates with Spatie Permission and provides a Filament-friendly UI and policies.
-   Workflow: contributor creates a record (status: draft) → editor/reviewer reviews and edits → publish.

Quick install (development):

-   Filament Shield (basic steps):

    1. Install the package (example): `composer require bezhanam/filament-shield` (verify package name/version before production install).
    2. Publish resources and run migrations as instructed by the package docs; configure Spatie Permission if not already installed.
    3. Use the Filament Shield UI to generate roles/permissions and seed an initial `admin` role.

These steps are minimal developer guidance; consult each package's README for exact install/config commands and version-specific instructions.

## Testing, QA & CI

-   Use Pest for tests (feature + Livewire). Write tests for: Work CRUD via Filament (Livewire), API endpoints (works list/detail, GeoJSON), and role-based access and publishing workflows.
-   CI: run Pest/phpunit, static analysis (PHPStan), and Pint formatting in CI pipelines.

## Migrations & naming conventions

-   Follow Laravel conventions: plural table names (`works`, `instances`, `items`, `agents`, `places`, `assets`, `subjects`, `agent_role`, `subject_work`).
-   Primary keys: unsigned bigint auto-increment by default;

    Note: For the MVP we'll use integer/unsigned bigint auto-increment primary keys. We plan a later migration to ULIDs/UUIDs (for distributed IDs and public-facing stability), but delaying that simplifies current migrations and Filament resource wiring.

-   Column names: snake_case. Include timestamps for main entities; use soft deletes on `works` and `instances` where appropriate.

## JSON vs normalization

-   Normalize: agents, places, subjects/tags, assets — because they are frequently queried and displayed in lists. Seller links will be stored as JSON on `works`.
-   JSON: alternative_titles, external_identifiers, raw payloads, and small editorial notes.

## Next steps (implementation priorities)

1. Scaffold models and migrations for `works`, `instances`, `items`, `agents`, `places`, `assets`, `subjects`.
2. Implement Filament resources for `Work` (with Instances & Agents relation managers).
3. Expose minimal public API endpoints: works list/detail and geo points.
4. Integrate Meilisearch for search indexing.
5. Configure storage disks and asset upload flows.
6. Write Pest tests for the implemented features.

## Developer notes

-   We use MySQL; be mindful of JSON query limitations compared to Postgres `jsonb`.
-   If advanced spatial queries or richer JSON indexing are required in the future, consider migrating to Postgres + PostGIS.
-   We will use Filament Shield for permissions and Spatie's Tags package (with a Filament integration) for tagging.
-   Keep table names unique (single management application) as requested.

---
