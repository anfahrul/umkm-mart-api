<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Merchant;
use App\Http\Resources\V1\ProductResource;

class MerchantProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'merchantID' => $this->merchant_id,
            'name' => $this->name,
            'merchantCategory' => Merchant::find($this->merchant_id)->productCategory->name,
            'address' => $this->address,
            'operationalTimeInOneDay' => $this->operational_time_oneday,
            'isOpen' => $this->is_open,
            'logo' => $this->logo,
            'description' => $this->description,
            'products' => ProductResource::collection(Merchant::find($this->merchant_id)->products),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}