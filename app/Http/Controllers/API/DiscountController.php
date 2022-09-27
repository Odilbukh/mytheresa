<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDiscountRequest;
use App\Http\Requests\GetDiscountListRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Services\DiscountService;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    private DiscountService $discountService;

    public function __construct(DiscountService $service)
    {
        $this->discountService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(GetDiscountListRequest $request)
    {
        return $this->discountService->index($request);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CreateDiscountRequest $request
     * @return Response
     */
    public function store(CreateDiscountRequest $request)
    {
        return $this->discountService->store($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->discountService->show($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDiscountRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateDiscountRequest $request, $id)
    {
        return $this->discountService->update($request->validated(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->discountService->destroy($id);
    }
}
