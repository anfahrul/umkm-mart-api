<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCategory;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product_thumbnail_path = Product::find($this->product_id)->productImage->first();
        // $product_thumbnail_path = ProductImage::where('product_id', $this->product_id)->first();

        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'merchant_id' => Product::find($this->product_id)->merchant->merchant_id,
            'product_category' => new ProductCategoryResource(ProductCategory::find($this->product_category_id)),
            'minimal_order' => $this->minimal_order,
            'short_desc' => $this->short_desc,
            'price_value' => $this->price_value,
            'stock_value' => $this->stock_value,
            'pict_thumbnail' => new ProductImageResource($product_thumbnail_path),
            'product_pictures' => ProductImageResource::collection(Product::find($this->product_id)->productImage),
        ];
    }
}
