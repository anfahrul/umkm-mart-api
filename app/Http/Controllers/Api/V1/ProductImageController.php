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

class ProductImageController extends Controller
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
        $productImage;

        if ($product === null) {
            return response()->json([
                'errors' => 'Product is not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            foreach ($request->file("images") as $image) {
                $originalImageName = str_replace( " ", "-", $image->getClientOriginalName());
                $imageName = Str::random(32) . '_' . $originalImageName;

                $productImage = new ProductImageResource(
                    ProductImage::create([
                        "product_id" => $product_id,
                        "file_path" => '/storage/productsLogo/' . $imageName,
                    ])
                );
                $image->storeAs('public/productsLogo', $imageName);
            }

            return $productImage;
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
    public function destroy(ProductImage $productImage)
    {
        //
    }
}
