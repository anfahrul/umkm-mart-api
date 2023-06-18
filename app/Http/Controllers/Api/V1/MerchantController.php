<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MerchantResource;
use App\Http\Resources\V1\MerchantProductsResource;
use App\Http\Resources\V1\MerchantCollection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request)
    {
        $productCategoryQuery = $request->query('product-category');
        if ($productCategoryQuery == null) {
            return new MerchantCollection(Merchant::latest()->get());
        } else {
            $productCategory = ProductCategory::where('slug', $productCategoryQuery)->first();
            if ($productCategory == null) {
                return response()->json([
                    'messages' => 'Product category not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $merchants = Merchant::where('product_category_id', $productCategory->id)->get();
            if (count($merchants) == 0) {
                return response()->json([
                    'messages' => 'Merchants not found in that category.'
                ], Response::HTTP_NOT_FOUND);
            } else {
                return new MerchantCollection($merchants);
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
        $merchantIsExist = Merchant::where('merchant_name', $request->merchant_name)->get();
        if (count($merchantIsExist) > 0) {
            return response()->json([
                'errors' => 'Merchant with this name is already exist. Please change the name!'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $merchantIsExist = Merchant::where('user_id', auth()->user()->id)->get();

            if (count($merchantIsExist) >= 1) {
                return response()->json([
                    'errors' => 'Merchant is already exist.'
                ], Response::HTTP_BAD_REQUEST);
            } else {
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

                return new MerchantResource(
                    Merchant::create(array_merge([
                        'user_id' => auth()->user()->id,
                        'merchant_name' => $merchantName,
                        'product_category_id' => $request->product_category_id,
                        'domain' => $domain,
                        'address' => $request->address,
                        'is_open' => 1,
                        'wa_number' => $request->wa_number,
                        'merchant_website_url' => $request->merchant_website_url,
                        'is_verified' => 0,
                        'original_logo_url' => '/storage/merchantsLogo/' . $logoName,
                        'operational_time_oneday' => $request->operational_time_oneday,
                        'description' => $request->description])
                    )
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Something went really wrong!'
                // 'errors' => $e
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($merchant_id)
    {
        $merchantIsExist = Merchant::find($merchant_id);
        if ($merchantIsExist === null) {
            return response()->json([
                'errors' => 'Merchant is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $merchantNew = Merchant::find($merchant_id);
            return new MerchantProductsResource($merchantNew);
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
    public function update(UpdateMerchantRequest $request, $merchant_id)
    {
        $merchant = Merchant::find($merchant_id);

        if ($merchant === null) {
            return response()->json([
                'errors' => 'Merchant is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {

            if (request()->hasFile('logo')){
                $logo = $request->file('logo');
                $originalLogoName = str_replace( " ", "-", $logo->getClientOriginalName());
                $logoName = Str::random(32) . '_' . $originalLogoName;

                //delete old logo
                $logoFromDatabase = substr($merchant->original_logo_url, 23);
                Storage::delete('public/merchantsLogo/'.$logoFromDatabase);

                //store new logo
                $logo->storeAs('public/merchantsLogo', $logoName);
                $merchant->original_logo_url = '/storage/merchantsLogo/' . $logoName;
            }

            if (request()->has('merchant_name')){
                $merchantIsExist = Merchant::where('merchant_name', $request->merchant_name)->get();
                if (count($merchantIsExist) > 0) {
                    return response()->json([
                        'errors' => 'Merchant with this name is already exist. Please change the name!'
                    ], Response::HTTP_BAD_REQUEST);
                } else {
                    $merchantName = $request->merchant_name;
                    $domainWithoutSpace = str_replace( " ", "-", $merchantName);
                    $domain = strtolower($domainWithoutSpace);

                    $merchant->merchant_name = $merchantName;
                    $merchant->domain = $domain;
                }
            }

            $merchant->product_category_id = $request->product_category_id;
            $merchant->address = $request->address;
            $merchant->is_open = $request->is_open;
            $merchant->wa_number = $request->wa_number;
            $merchant->merchant_website_url = $request->merchant_website_url;
            $merchant->operational_time_oneday = $request->operational_time_oneday;
            $merchant->description = $request->description;

            $merchant->save();
            return new MerchantResource($merchant);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($merchant_id)
    {
        $merchant = Merchant::find($merchant_id);

        if ($merchant === null) {
            return response()->json([
                'errors' => 'Merchant is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $products = Product::where('merchant_id', $merchant_id)->get();
            if (count($products) > 0) {
                return response()->json([
                    'messages' => "Delete all owned products before deleting the merchant"
                ], Response::HTTP_BAD_REQUEST);
            }

            //delete logo from storage
            $logoFromDatabase = substr($merchant->original_logo_url, 23);
            Storage::delete('public/merchantsLogo/'.$logoFromDatabase);

            $merchant->delete();

            return response()->json([
                'messages' => 'Merchant is deleted successful.'
            ], Response::HTTP_OK);
        }
    }
}
