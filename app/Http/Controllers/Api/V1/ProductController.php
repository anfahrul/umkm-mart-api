<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Merchant;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductImageResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show']]);
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
    public function store(StoreProductRequest $request, $merchant_id)
    {
        $merchntasIsNotExist = Merchant::find($merchant_id);
        $product;

        if ($merchntasIsNotExist === null) {
            return response()->json([
                'errors' => 'Merchant is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $merchantNew = Merchant::find($merchant_id);

            $product = new ProductResource(
                Product::create(array_merge([
                    'merchant_id' => $merchant_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description,
                    'product_category_id' => $request->product_category_id,
                    'is_available' => 1])
                )
            );

            if (request()->hasFile('images')){
                foreach ($request->file("images") as $image) {
                    $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                    $imageName = Str::random(32) . '_' . $originalImageName;

                    new ProductImageResource(
                        ProductImage::create([
                            "product_id" => $product->product_id,
                            "file_path" => '/storage/productsLogo/' . $imageName,
                        ])
                    );
                    $image->storeAs('public/productsLogo', $imageName);
                }
            }
        }

        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
    {
        $productIsExist = Product::find($product_id);
        if ($productIsExist === null) {
            return response()->json([
                'errors' => 'Product is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $merchantNew = Product::find($product_id);
            return new ProductResource($merchantNew);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $product_id)
    {
        $product = Product::find($product_id);

        if ($product === null) {
            return response()->json([
                'errors' => 'Product is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {

            if (request()->hasFile('image')){
                $image = $request->file('image');
                $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                $imageName = Str::random(32) . '_' . $originalImageName;

                //delete old logo
                $imageFromDatabase = substr($product->image, 22);
                Storage::delete('public/productsLogo/'.$imageFromDatabase);

                //store new logo
                $image->storeAs('public/productsLogo', $imageName);
                $product->image = '/storage/productsLogo/' . $imageName;
            }

            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->product_category_id = $request->product_category_id;
            $product->is_available = $request->is_available;

            $product->save();
            return new ProductResource($product);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product_id)
    {
        $product = Product::find($product_id);

        if ($product === null) {
            return response()->json([
                'errors' => 'Product is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $productImages = ProductImage::where('product_id', $product_id)->get();

            foreach ($productImages as $images => $value){
                //delete logo from storage
                $imageFromDatabase = substr($value->file_path, 22);
                Storage::delete('public/productsLogo/'.$imageFromDatabase);

                // Delete row
                $productImageDeleted = ProductImage::find($value->id);
                $productImageDeleted->delete();
            }

            $product->delete();

            return response()->json([
                'messages' => 'Product is deleted successful.'
            ], Response::HTTP_OK);
        }
    }
}
