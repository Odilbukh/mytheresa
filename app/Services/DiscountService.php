<?php

namespace App\Services;

use App\Models\Discount;

class DiscountService
{
    public function index($request)
    {
        $model = Discount::paginate($request['size']);

        return $model;
    }

    public function store($request)
    {
        $model = Discount::create($request);

        return $model;
    }

    public function show($id)
    {
        $model = Discount::findOrFail($id);

        return $model;
    }

    public function update($request, $id)
    {
        $model = Discount::findOrFail($id);

        $model->update($request);

        return $model;
    }

    public function destroy($id)
    {
        $model = Discount::findOrFail($id);

        $model->delete();

        return $model;
    }
}