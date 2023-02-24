<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Merchant;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MerchantResource;
use App\Http\Resources\V1\MerchantProductsResource;
use App\Http\Resources\V1\MerchantCollection;

class MerchantController extends Controller
{
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchant $merchant)
    {
        return new MerchantProductsResource($merchant);
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
