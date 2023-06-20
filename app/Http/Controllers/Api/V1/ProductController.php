<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Merchant;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductStoreResponseResource;
use App\Http\Resources\V1\ProductDeleteResponseResource;
use App\Http\Resources\V1\ProductImageResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\V1\ProductCollection;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;

class ProductController extends ApiController
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
        $productCategoryQuery = $request->query('product-category-slug');
        if ($productCategoryQuery == null) {
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new ProductCollection(Product::latest()->get()),
                Response::HTTP_OK
            );
        } else {
            $productCategory = ProductCategory::where('slug', $productCategoryQuery)->first();
            if ($productCategory == null) {
                return $this->errorResponse(
                    Response::HTTP_NOT_FOUND . " Not Found",
                    "Product with category " . $productCategoryQuery . " is not found",
                    Response::HTTP_NOT_FOUND
                );
            }

            $products = Product::where('product_category_id', $productCategory->id)->get();
            if (count($products) == 0) {
                return $this->errorResponse(
                    Response::HTTP_NOT_FOUND . " Not Found",
                    "Products with category id " . $productCategory->id . " is not found",
                    Response::HTTP_NOT_FOUND
                );
            } else {
                return $this->successResponse(
                    Response::HTTP_OK . " OK",
                    new ProductCollection($products),
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
    public function store(StoreProductRequest $request, $merchant_id)
    {
        $merchntasIsNotExist = Merchant::find($merchant_id);
        $product;

        if ($merchntasIsNotExist === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Merchant with id " . $merchant_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $merchantNew = Merchant::find($merchant_id);

            $product = Product::create(array_merge([
                'name' => $request->name,
                'merchant_id' => $merchant_id,
                'product_category_id' => $request->product_category_id,
                'minimal_order' => $request->minimal_order,
                'short_desc' => $request->short_desc,
                'price_value' => $request->price_value,
                'stock_value' => $request->stock_value])
            );

            if (request()->hasFile('images')){
                foreach ($request->file("images") as $image) {
                    $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                    $imageName = Str::random(32) . '_' . $originalImageName;
                    $pict_thumbnail_name = $imageName;

                    new ProductImageResource(
                        ProductImage::create([
                            "product_id" => $product->product_id,
                            "file_path" => '/storage/productImages/' . $imageName,
                        ])
                    );
                    $image->storeAs('public/productImages', $imageName);
                }
            }
        }

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new ProductStoreResponseResource($product),
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
    {
        $productIsExist = Product::find($product_id);
        if ($productIsExist === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product with id " . $product_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $merchantNew = Product::find($product_id);
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new ProductResource($merchantNew),
                Response::HTTP_OK
            );
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
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product with id " . $product_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            if (request()->hasFile('images')){
                $productImages = ProductImage::where('product_id', $product_id)->get();

                foreach ($productImages as $images => $value){
                    //delete logo from storage
                    $imageFromDatabase = substr($value->file_path, 22);
                    Storage::delete('public/productImages/'.$imageFromDatabase);

                    // Delete row
                    $productImageDeleted = ProductImage::find($value->id);
                    $productImageDeleted->delete();
                }

                foreach ($request->file("images") as $image) {
                    $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                    $imageName = Str::random(32) . '_' . $originalImageName;

                    new ProductImageResource(
                        ProductImage::create([
                            "product_id" => $product_id,
                            "file_path" => '/storage/productImages/' . $imageName,
                        ])
                    );
                    $image->storeAs('public/productImages', $imageName);
                }
            }

            $product->name = $request->name;
            $product->product_category_id = $request->product_category_id;
            $product->minimal_order = $request->minimal_order;
            $product->minimal_order = $request->minimal_order;
            $product->price_value = $request->price_value;
            $product->stock_value = $request->stock_value;

            $product->save();
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new ProductStoreResponseResource($product),
                Response::HTTP_OK
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product_id)
    {
        $product = Product::find($product_id);

        if ($product === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product with id " . $product_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $productImages = ProductImage::where('product_id', $product_id)->get();

            foreach ($productImages as $images => $value){
                //delete logo from storage
                $imageFromDatabase = substr($value->file_path, 22);
                Storage::delete('public/productImages/'.$imageFromDatabase);

                // Delete row
                $productImageDeleted = ProductImage::find($value->id);
                $productImageDeleted->delete();
            }

            $product->delete();

            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new ProductStoreResponseResource($product),
                Response::HTTP_OK
            );
        }
    }
}
