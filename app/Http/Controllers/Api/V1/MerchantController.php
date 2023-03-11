<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Merchant;
use App\Models\ProductCategory;
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

                return new MerchantResource(
                    Merchant::create(array_merge([
                        'name' => $request->name,
                        'product_category_id' => $request->product_category_id,
                        'user_id' => auth()->user()->id,
                        'address' => $request->address,
                        'operational_time_oneday' => $request->operational_time_oneday,
                        'logo' => '/storage/merchantsLogo/' . $logoName,
                        'is_open' => 1,
                        'description' => $request->description])
                    )
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Something went really wrong!'
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
                $logoFromDatabase = substr($merchant->logo, 23);
                Storage::delete('public/merchantsLogo/'.$logoFromDatabase);

                //store new logo
                $logo->storeAs('public/merchantsLogo', $logoName);
                $merchant->logo = '/storage/merchantsLogo/' . $logoName;
            }

            $merchant->name = $request->name;
            $merchant->product_category_id = $request->product_category_id;
            $merchant->address = $request->address;
            $merchant->operational_time_oneday = $request->operational_time_oneday;
            $merchant->is_open = $request->is_open;
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
            //delete logo from storage
            $logoFromDatabase = substr($merchant->logo, 23);
            Storage::delete('public/merchantsLogo/'.$logoFromDatabase);

            $merchant->delete();

            return response()->json([
                'messages' => 'Merchant is deleted successful.'
            ], Response::HTTP_OK);
        }
    }
}
