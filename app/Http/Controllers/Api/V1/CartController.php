<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Controllers\Api\V1\ApiController;
use Symfony\Component\HttpFoundation\Response;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\V1\CartStoreResponseResource;
use App\Http\Resources\V1\CartResource;
use App\Http\Resources\V1\CartWithDetailsResource;

class CartController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = Customer::where('user_id', auth()->user()->id)->first();
        $carts = $users = DB::table('carts')
                ->where('customer_id', '=', $customer->id)
                ->where('total', '>', 0)
                ->get();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            CartResource::collection($carts),
            Response::HTTP_OK
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $product_id)
    {
        $product = Product::find($product_id);
        if ($product === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product with id " . $product_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $validator = Validator::make($request->all(), [
            'quantity_of_product' => 'required|integer|min:' . $product->minimal_order,
        ]);
        if ($validator->fails()) {
            return $this->errorResponse(
                Response::HTTP_UNPROCESSABLE_ENTITY . " Unprocessable Content",
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
         }

        $customer = Customer::where('user_id', auth()->user()->id)->first();
        $carts = DB::table('carts')
                ->where('customer_id', '=', $customer->id)
                ->where('merchant_id', '=', $product->merchant_id)
                ->first();

        if ($carts === null) {
            $cart_id = Str::uuid()->toString();

            $carts = Cart::create(array_merge([
                'id' => $cart_id,
                'customer_id' => $customer->id,
                'merchant_id' => $product->merchant_id,
                'total' => 0])
            );
        }

        $qtyOfproductRequested = $request->quantity_of_product;
        $price = $qtyOfproductRequested * $product->price_value;

        $cartDetail = CartDetail::create(array_merge([
            'cart_id' => $carts->id,
            'product_id' => $product->product_id,
            'price' => $price,
            'quantity' => $qtyOfproductRequested])
        );

        $existingCart = Cart::find($carts->id);
        $existingCart->total = $existingCart->total + $price;
        $existingCart->save();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new CartStoreResponseResource($cartDetail),
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart, $cart_id)
    {
        $cart = Cart::find($cart_id);
        if ($cart === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Cart with id " . $cart_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new CartWithDetailsResource($cart),
            Response::HTTP_OK
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
