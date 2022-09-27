<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{
    // filter by category
    public function category($search_string)
    {
        $this->builder->where(function (Builder $query) use ($search_string) {
            $query->where('category', $search_string);
        });
    }

    // filter products by discount percentage
    public function discount($search_string)
    {
        $this->builder->whereHas('discount', function (Builder $query) use ($search_string) {
            $query->where('percentage', $search_string);
        });
    }

    // filter products with prices lesser than or equal the value provided
    public function priceLess($search_string)
    {
        $this->builder->where(function (Builder $query) use ($search_string) {
            $query->where('price', '<=', $search_string);
        });
    }
}