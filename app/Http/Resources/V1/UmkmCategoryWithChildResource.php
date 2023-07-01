<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\ProductCategoryResource;
use App\Models\UmkmCategory;

class UmkmCategoryWithChildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'childs_categories' => ProductCategoryResource::collection(UmkmCategory::find($this->id)->childs),
        ];
    }
}
