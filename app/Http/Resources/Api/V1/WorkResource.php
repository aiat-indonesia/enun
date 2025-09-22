<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'type' => $this->type,
            'summary' => $this->summary,
            'languages' => $this->languages,
            'subjects' => $this->whenLoaded('subjects'),
            'agents' => $this->whenLoaded('agents'),
            'primary_place' => $this->whenLoaded('primaryPlace'),
            'instances_count' => $this->when($this->instances_count, $this->instances_count),
            'instances' => $this->whenLoaded('instances'),
            'metadata' => $this->metadata,
            'external_identifiers' => $this->external_identifiers,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
