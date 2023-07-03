<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CartDetail;
use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\StoreCartDetailRequest;
use App\Http\Requests\UpdateCartDetailRequest;
use App\Http\Controllers\Api\V1\ApiController;
use Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Http\Resources\V1\CartDetailsUpdateResponseResource;
use App\Http\Resources\V1\CartDetailsDeleteResponseResource;

class CartDetailController extends ApiController
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
        //
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
    public function store(StoreCartDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CartDetail $cartDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CartDetail $cartDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartDetail $cartDetail, $cart_detail_id)
    {
        $newcartDetail = CartDetail::find($cart_detail_id);
        if ($newcartDetail === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Details of cart with id " . $cart_detail_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $product = Product::find($newcartDetail->product_id);

        $validator = Validator::make($request->all(), [
            'new_quantity' => 'required|integer|min:' . $product->minimal_order,
        ]);
        if ($validator->fails()) {
            return $this->errorResponse(
                Response::HTTP_UNPROCESSABLE_ENTITY . " Unprocessable Content",
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //update detail of cart
        $newQtyRequested = $request->new_quantity;
        $price = $newQtyRequested * $product->price_value;

        $newcartDetail->quantity = $newQtyRequested;
        $newcartDetail->price = $price;
        $newcartDetail->save();

        //update price in cart
        $newTotalPrice = 0;
        $cartDetails = CartDetail::where('cart_id', $newcartDetail->cart_id)->get();
        foreach ($cartDetails as $cartDetail => $value){
            $newTotalPrice += $value->price;
        }
        $existingCart = Cart::find($newcartDetail->cart_id);
        $existingCart->total = $newTotalPrice;
        $existingCart->save();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new CartDetailsUpdateResponseResource($newcartDetail),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartDetail $cartDetail, $cart_detail_id)
    {
        $cartDetail = CartDetail::find($cart_detail_id);
        if ($cartDetail === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Details of cart with id " . $cart_detail_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $priceOfCartDetail = $cartDetail->price;
        $existingCart = Cart::find($cartDetail->cart_id);

        $existingCart->total = $existingCart->total - $priceOfCartDetail;
        $existingCart->save();
        $cartDetail->delete();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new CartDetailsDeleteResponseResource($cartDetail),
            Response::HTTP_OK
        );
    }
}
