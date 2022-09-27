<?php

namespace App\Http\Resources;

use App\Models\Discount;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->discount_id) {
            $discount = Discount::find($this->discount_id);
            $final = $this->price - (($this->price * $discount->percentage) / 100);
        }

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'price' => [
                "original" => $this->price,
                "final" => ($this->discount_id) ? $final : $this->price,
                "discount_percentage" => ($this->discount_id) ? "{$discount->percentage}%" : NULL,
                "currency" => ($this->discount_id) ? $discount->currency : "EUR"
            ]
        ];
    }
}
