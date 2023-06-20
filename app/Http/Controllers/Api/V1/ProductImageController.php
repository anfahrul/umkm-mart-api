<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProductImage;
use App\Models\Product;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Http\Resources\V1\ProductImageResource;
use App\Http\Resources\V1\ProductImageStoreResponseResource;
use App\Http\Resources\V1\ProductImageDeleteResponseResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\V1\ApiController;

class ProductImageController extends ApiController
{
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
    public function store(StoreProductImageRequest $request, $product_id)
    {
        $product = Product::find($product_id);

        if ($product === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product with id " . $product_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $productImages = [];

            foreach ($request->file("images") as $image) {
                $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                $imageName = Str::random(32) . '_' . $originalImageName;

                $productImage = ProductImage::create(array_merge([
                    "id" => Str::random(8),
                    "product_id" => $product_id,
                    "file_path" => '/storage/productImages/' . $imageName,
                ]));

                array_push($productImages, $productImage);

                $image->storeAs('public/productImages', $imageName);
            }

            return $this->successResponse(
                Response::HTTP_CREATED . " Created",
                ProductImageStoreResponseResource::collection($productImages),
                Response::HTTP_CREATED
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductImageRequest $request, ProductImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product_image_id)
    {
        $productImage = ProductImage::find($product_image_id);

        if ($productImage === null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product image with id " . $product_image_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        } else {
            $productImageRes = ProductImage::find($product_image_id)->first();

            $imageFromDatabase = substr($productImageRes->file_path, 22);
            Storage::delete('public/productImages/'.$imageFromDatabase);

            $productImage->delete();

            return $this->successResponse(
                Response::HTTP_OK . " OK",
                new ProductImageDeleteResponseResource($productImage),
                Response::HTTP_OK
            );
        }
    }
}
