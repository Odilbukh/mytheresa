<?php

namespace App\Http\Controllers\API;

use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\GetListProductsRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    private ProductService $productService;

    public function __construct(ProductService $service)
    {
        $this->productService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(GetListProductsRequest $request, ProductFilter $filter)
    {
        return $this->productService->index($request->validated(), $filter);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProductRequest $request
     * @return Response
     */
    public function store(CreateProductRequest $request)
    {
        return $this->productService->store($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->productService->show($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        return $this->productService->update($request->validated(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->productService->destroy($id);
    }
}
