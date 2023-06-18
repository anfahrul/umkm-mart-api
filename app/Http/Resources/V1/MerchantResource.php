<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Merchant;
use App\Models\ProductCategory;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'merchant_id' => $this->merchant_id,
            'user_id' => $this->user_id,
            'merchant_name' => $this->merchant_name,
            'merchant_category' => Merchant::find($this->merchant_id)->productCategory->name,
            'domain' => $this->domain,
            'address' => $this->address,
            'is_open' => $this->is_open,
            'wa_number' => $this->wa_number,
            'merchant_website_url' => $this->merchant_website_url,
            'is_verified' => $this->is_verified,
            'logo' => $this->original_logo_url,
            'operational_time_oneday' => $this->operational_time_oneday,
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
