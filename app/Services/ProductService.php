<?php

namespace App\Services;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductService
{
    public function index($request, $filter)
    {
        $model = Product::filter($filter)->paginate($request['size']);

        return response(['products' => ProductListResource::collection($model)]);
    }

    public function store($request)
    {
        $model = Product::create($request);

        return response(new ProductResource($model));
    }

    public function show($id)
    {
        $model = Product::findOrFail($id);

        return response(new ProductResource($model));
    }


    public function update($request, $id)
    {
        $model = Product::findOrFail($id);

        $model->update($request);

        return response(new ProductResource($model));
    }

    public function destroy($id)
    {
        $model = Product::findOrFail($id);

        $model->delete();

        return response('Product deleted successfully.');
    }
}