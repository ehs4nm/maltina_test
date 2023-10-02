<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'type' => $this->when($this->type, [
                'id' => $this->type?->id,
                'name' => $this->type?->name,
                'options' => $this->type ? OptionResource::collection($this->type?->options) : null, // Return Option collection if product has a type
            ]),
        ];
    }
}
