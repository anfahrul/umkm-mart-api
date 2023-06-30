<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProductCategory;
use App\Models\UmkmCategory;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductCategoryResource;
use App\Http\Resources\V1\ProductCategoryCollection;
use App\Http\Controllers\Api\V1\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\V1\ProductCategoriesStoreResponseResource;
use App\Http\Resources\V1\ProductCategoriesDeleteResponseResource;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:system-admin', ['except' => ['index']]);
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
                new ProductCategoryCollection(ProductCategory::latest()->get()),
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

            $productCategory = ProductCategory::where('umkm_category_id', $umkmCategory->id)->get();
            if (count($productCategory) == 0) {
                return $this->successResponse(
                    Response::HTTP_NOT_FOUND . " Not Found",
                    null,
                    Response::HTTP_NOT_FOUND
                );
            } else {
                return $this->successResponse(
                    Response::HTTP_OK . " OK",
                    new ProductCategoryCollection($productCategory),
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
    public function store(StoreProductCategoryRequest $request)
    {
        $slugWithoutSpace = str_replace(" ", "-", $request->name);
        $slug = strtolower($slugWithoutSpace);

        $productCategoryIsExist = ProductCategory::where('slug', $slug)->first();
        if ($productCategoryIsExist != null) {
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                "Product category " . $request->name . " is already exist",
                Response::HTTP_BAD_REQUEST
            );
        }

        $productCategory = ProductCategory::create(array_merge([
            'name' => $request->name,
            'slug' => $slug,
            'umkm_category_id' => $request->umkm_category_id])
        );

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new ProductCategoriesStoreResponseResource($productCategory),
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory, $slug)
    {
        $productCategoryIsExist = ProductCategory::where('slug', $slug)->first();
        if ($productCategoryIsExist == null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Product category with slug " . $slug . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }


        $productCategory = ProductCategory::find($productCategoryIsExist->id);
        $productCategory->delete();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new ProductCategoriesDeleteResponseResource($productCategory),
            Response::HTTP_OK
        );
    }
}
