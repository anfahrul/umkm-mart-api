<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\UmkmCategory;
use App\Models\Product;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MerchantResource;
use App\Http\Resources\V1\MerchantProductsResource;
use App\Http\Resources\V1\MerchantStoreResponseResource;
use App\Http\Resources\V1\MerchantUpdateResponseResource;
use App\Http\Resources\V1\MerchantDeleteResponseResource;
use App\Http\Resources\V1\MerchantCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\V1\ApiController;

class MerchantController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:user', ['except' => ['index','show','showByDomain','getLogo']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $umkmCategoryQuery = $request->query('umkm-category-slug');
        if ($umkmCategoryQuery == null) {
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new MerchantCollection(Merchant::latest()->get()),
                Response::HTTP_OK
            );
        } else {
            $umkmCategory = UmkmCategory::where('slug', $umkmCategoryQuery)->first();
            if ($umkmCategory == null) {
                return $this->errorResponse(
                    Response::HTTP_NOT_FOUND . " Not Found",
                    "Category " . $umkmCategoryQuery . " is not found",
                    Response::HTTP_NOT_FOUND
                );
            }

            $merchants = Merchant::where('umkm_category_id', $umkmCategory->id)->get();
            if (count($merchants) == 0) {
                return $this->successResponse(
                    Response::HTTP_NOT_FOUND . " Not Found",
                    null,
                    Response::HTTP_NOT_FOUND
                );
            } else {
                return $this->successResponse(
                    Response::HTTP_OK . " OK",
                    new MerchantCollection($merchants),
                    Response::HTTP_OK
                );
            }
        }
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
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                'Your merchant is already exist',
                Response::HTTP_BAD_REQUEST
            );
        }

        $merchantIsExist = Merchant::where('merchant_name', $request->merchant_name)->get();
        if (count($merchantIsExist) > 0) {
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                'Name ' . $request->merchant_name . ' is already in use. Please change the name',
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $logoName = '';

            if (request()->hasFile('logo')){
                $logo = $request->file('logo');
                $originalLogoName = str_replace( " ", "-", $logo->getClientOriginalName());
                $logoName = Str::random(32) . '_' . $originalLogoName;

                $logo->storeAs('public/merchantsLogo', $logoName);
            }else{
                $logoName = 'default-logo.jpg';
            }

            $merchantName = $request->merchant_name;
            $domainWithoutSpace = str_replace( " ", "-", $merchantName);
            $domain = strtolower($domainWithoutSpace);

            $merchant = Merchant::create(array_merge([
                'user_id' => auth()->user()->id,
                'merchant_name' => $merchantName,
                'umkm_category_id' => $request->umkm_category_id,
                'domain' => $domain,
                'address' => $request->address,
                'is_open' => 1,
                'wa_number' => $request->wa_number,
                'merchant_website_url' => $request->merchant_website_url,
                'is_verified' => 0,
                'original_logo_url' => $logoName,
                'operational_time_oneday' => $request->operational_time_oneday,
                'description' => $request->description])
            );

            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new MerchantStoreResponseResource($merchant),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Something went really wrong!'
                // 'errors' => $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the merchant's logo.
     */
    public function getLogo($filename)
    {
        $path = public_path('/storage/merchantsLogo/' . $filename);

        if (file_exists($path)) {
            return response()->file($path);
        }

        return $this->errorResponse(
            Response::HTTP_NOT_FOUND . " Not Found",
            "Merchant's logo is not found",
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($merchant_id)
    {
        $merchantIsExist = Merchant::find($merchant_id);
        if ($merchantIsExist === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Merchant with id " . $merchant_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $merchantNew = Merchant::find($merchant_id);

            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new MerchantProductsResource($merchantNew),
                Response::HTTP_OK
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function showByDomain($domain)
    {
        $merchant = Merchant::where('domain', $domain)->first();
        if ($merchant === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Merchant with domain " . $domain . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new MerchantProductsResource($merchant),
                Response::HTTP_OK
            );
        }
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
    public function update(UpdateMerchantRequest $request)
    {
        $merchant = Merchant::where('user_id', auth()->user()->id)->first();

        if (request()->hasFile('logo')){
            $logo = $request->file('logo');
            $originalLogoName = str_replace( " ", "-", $logo->getClientOriginalName());
            $logoName = Str::random(32) . '_' . $originalLogoName;

            //delete old logo
            $logoFromDatabase = $merchant->original_logo_url;
            if (substr($logoFromDatabase, 0, 12) != "default-logo") {
                Storage::delete('public/merchantsLogo/'.$logoFromDatabase);
            }

            //store new logo
            $logo->storeAs('public/merchantsLogo', $logoName);
            $merchant->original_logo_url = $logoName;
        }

        if (request()->has('merchant_name')){
            $merchantIsExist = Merchant::where('merchant_name', $request->merchant_name)->get();
            if (count($merchantIsExist) > 0) {
                return $this->errorResponse(
                    Response::HTTP_BAD_REQUEST . " Bad Request",
                    'Name ' . $request->merchant_name . ' is already in use. Please change the name',
                    Response::HTTP_BAD_REQUEST
                );
            } else {
                $merchantName = $request->merchant_name;
                $domainWithoutSpace = str_replace( " ", "-", $merchantName);
                $domain = strtolower($domainWithoutSpace);

                $merchant->merchant_name = $merchantName;
                $merchant->domain = $domain;
            }
        }

        $merchant->umkm_category_id = $request->umkm_category_id;
        $merchant->address = $request->address;
        $merchant->is_open = $request->is_open;
        $merchant->wa_number = $request->wa_number;
        $merchant->merchant_website_url = $request->merchant_website_url;
        $merchant->operational_time_oneday = $request->operational_time_oneday;
        $merchant->description = $request->description;

        $merchant->save();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new MerchantUpdateResponseResource($merchant),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $merchant = Merchant::where('user_id', auth()->user()->id)->first();

        $products = Product::where('merchant_id', $merchant->merchant_id)->get();
        if (count($products) > 0) {
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                "Delete all owned products before deleting the merchant",
                Response::HTTP_BAD_REQUEST
            );
        }

        //delete logo from storage
        $logoFromDatabase = $merchant->original_logo_url;
        if (substr($logoFromDatabase, 0, 12) != "default-logo") {
            Storage::delete('public/merchantsLogo/'.$logoFromDatabase);
        }

        $merchant->delete();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new MerchantDeleteResponseResource($merchant),
            Response::HTTP_OK
        );
    }
}
