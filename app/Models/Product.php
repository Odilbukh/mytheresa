<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'category',
        'price',
        'discount_id'
    ];

    public static function booted()
    {
        $discount30 = Discount::where('percentage', 30)->first()->id;
        $discount15 = Discount::where('percentage', 15)->first()->id;

        self::creating(function ($product) use ($discount15, $discount30) {
            // format double to integer
            $product->price = $product->price * 100;

            // set discounts under certain conditions
            if ($product->sku === '000003') {
                $product->discount_id = $discount15;
            }
            if ($product->category === 'boots') {
                $product->discount_id = $discount30;
            }
        });

        self::updating(function ($product) use ($discount15, $discount30) {
            // format double to integer
            $product->price = $product->price * 100;


            $oldData = $product->getOriginal();

            // compare discount percentages before and after update and set the biggest one
            if (isset($oldData['discount_id']) and !is_null($product->discount_id)) {
                if ($product->getPercentage($oldData['discount_id']) > $product->getPercentage($product->discount_id)) {
                    $product->discount_id = $oldData['discount_id'];
                }
            }

            // set discounts under certain conditions
            if ($product->sku === '000003') {
                $product->discount_id = $discount15;
            }

            if ($product->category === 'boots') {
                $product->discount_id = $discount30;
            }
        });
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        $filter->apply($builder);
    }

    public function getPercentage(int $discount)
    {
        return Discount::where('id', $discount)->select('percentage')->first()->percentage;
    }
}
