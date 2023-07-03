<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;

class CartDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cart_details_id' => $this->id,
            'product' => new ProductResource(Product::find($this->product_id)),
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }
}
