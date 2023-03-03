<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Merchant;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MerchantResource;
use App\Http\Resources\V1\MerchantProductsResource;
use App\Http\Resources\V1\MerchantCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MerchantController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new MerchantCollection(Merchant::latest()->get());;
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
    public function store(StoreMerchantRequest $request)
    {
        $merchantIsExist = Merchant::where('user_id', auth()->user()->id)->get();

        if (count($merchantIsExist) >= 1) {
            return response()->json([
                'errors' => 'Merchant is already exist.'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            return new MerchantResource(
                Merchant::create(array_merge([
                    'name' => $request->name,
                    'product_category_id' => $request->product_category_id,
                    'user_id' => auth()->user()->id,
                    'address' => $request->address,
                    'operational_time_oneday' => $request->operational_time_oneday,
                    'logo' => $request->logo,
                    'is_open' => 1,
                    'description' => $request->description])
                )
            );
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($merchant_id)
    {
        $merchantNew = Merchant::find($merchant_id);
        return new MerchantProductsResource($merchantNew);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchant $merchant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
}
