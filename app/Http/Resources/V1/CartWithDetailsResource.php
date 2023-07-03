<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Merchant;
use App\Models\Customer;
use App\Models\Cart;
use App\Http\Resources\V1\CartDetailsResource;

class CartWithDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->id,
            'customer' => new CustomerResource(Customer::find($this->customer_id)),
            'merchant' => new MerchantResource(Merchant::find($this->merchant_id)),
            'cart_details' => CartDetailsResource::collection(Cart::find($this->id)->cartDetails),
            'total_price' => $this->total,
        ];
    }
}
