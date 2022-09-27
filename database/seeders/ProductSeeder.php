<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            "sku" => "000001",
            "name" => "BV Lean leather ankle boots",
            "category" => "boots",
            "price" => 890.00
        ]);

        Product::create([
            "sku" => "000002",
            "name" => "BV Lean leather ankle boots",
            "category" => "boots",
            "price" => 990.00
        ]);

        Product::create([
            "sku" => "000003",
            "name" => "Ashlington leather ankle boots",
            "category" => "boots",
            "price" => 710.00
        ]);

        Product::create([
            "sku" => "000003",
            "name" => "Ashlington leather",
            "category" => "kids",
            "price" => 210.00
        ]);

        Product::create([
            "sku" => "000004",
            "name" => "Naima embellished suede sandals",
            "category" => "sandals",
            "price" => 79500
        ]);

        Product::create([
            "sku" => "000005",
            "name" => "Nathane leather sneakers",
            "category" => "sneakers",
            "price" => 590.00
        ]);

        Product::create([
            "sku" => "000006",
            "name" => "MARC JACOBS KIDS",
            "category" => "kids",
            "price" => 450.00
        ]);

        Product::create([
            "sku" => "000007",
            "name" => "STELLA MCCARTNEY KIDS",
            "category" => "kids",
            "price" => 1350.00
        ]);
    }
}
