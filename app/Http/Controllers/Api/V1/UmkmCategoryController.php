<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UmkmCategory;
use App\Models\ProductCategory;
use App\Http\Requests\StoreUmkmCategoryRequest;
use App\Http\Requests\UpdateUmkmCategoryRequest;
use App\Http\Controllers\Api\V1\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\V1\UmkmCategoryResource;
use App\Http\Resources\V1\UmkmCategoryWithChildResource;
use App\Http\Resources\V1\UmkmCategoriesStoreResponseResource;
use App\Http\Resources\V1\UmkmCategoriesDeleteResponseResource;
use Illuminate\Http\Request;

class UmkmCategoryController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:system-admin', ['except' => ['index','getChild']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $umkmCategory = UmkmCategory::latest()->get();
        if (count($umkmCategory) == 0) {
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                [],
                Response::HTTP_OK
            );
        } else {
            return $this->successResponse(
                Response::HTTP_OK . " OK",
                UmkmCategoryResource::collection($umkmCategory),
                Response::HTTP_OK
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getChild($slug)
    {
        $umkmCategory = UmkmCategory::where('slug', $slug)->first();
        if ($umkmCategory == null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Category with slug " . $slug . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new UmkmCategoryWithChildResource($umkmCategory),
            Response::HTTP_OK
        );
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
    public function store(StoreUmkmCategoryRequest $request)
    {
        $slugWithoutSpace = str_replace(" ", "-", $request->name);
        $slug = strtolower($slugWithoutSpace);

        $umkmCategoryIsExist = UmkmCategory::where('slug', $slug)->first();
        if ($umkmCategoryIsExist != null) {
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                "UMKM category " . $request->name . " is already exist",
                Response::HTTP_BAD_REQUEST
            );
        }

        $umkmCategory = UmkmCategory::create(array_merge([
            'name' => $request->name,
            'slug' => $slug])
        );

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new UmkmCategoriesStoreResponseResource($umkmCategory),
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(UmkmCategory $umkmCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UmkmCategory $umkmCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUmkmCategoryRequest $request, UmkmCategory $umkmCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UmkmCategory $umkmCategory, $slug)
    {
        $umkmCategoryIsExist = UmkmCategory::where('slug', $slug)->first();
        if ($umkmCategoryIsExist == null) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "UMKM category with slug " . $slug . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }


        $umkmCategory = UmkmCategory::find($umkmCategoryIsExist->id);
        $childs = UmkmCategory::find($umkmCategoryIsExist->id)->childs;

        foreach ($childs as $child => $value){
            // Delete row
            $childDeleted = ProductCategory::find($value->id);
            $childDeleted->delete();
        }

        $umkmCategory->delete();

        return $this->successResponse(
            Response::HTTP_OK . " OK",
            new UmkmCategoriesDeleteResponseResource($umkmCategory),
            Response::HTTP_OK
        );
    }
}
