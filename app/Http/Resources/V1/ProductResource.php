<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;

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
            'productID' => $this->product_id,
            'merchantID' => Product::find($this->product_id)->merchant->merchant_id,
            'name' => $this->name,
            'price' => $this->price,
            'images' => ProductImageResource::collection(Product::find($this->product_id)->productImage),
            'description' => $this->description,
            'productCategory' => Product::find($this->product_id)->productCategory->name,
            'isAvailable' => $this->is_available,
        ];
    }
}
