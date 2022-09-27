<?php

namespace Tests\Unit;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }

    /**
     * Test getting of products list
     * and assert the count of list in per page
     *
     * @return void
     */
    public function test_get_list_products()
    {
        $response = $this->json('GET', '/api/products?page=1&size=5');

        $response->assertOk();
        $response->assertJsonCount(5, 'products');
    }

    /**
     * Test getting of products list
     * and assert the validation errors
     *
     * @return void
     */
    public function test_validation_on_get_list_products()
    {
        $response = $this->json('GET', '/api/products');

        $response->assertJsonValidationErrors(['page', 'size']);
    }

    /**
     * Test getting of products list
     * and filter by category
     *
     * @return void
     */
    public function test_get_list_products_by_category()
    {
        $response = $this->json('GET', '/api/products?page=1&size=10&category=boots');

        $response->assertOk();
        $response->assertJsonFragment([
            'category' => 'boots'
        ]);
    }

    /**
     * Test getting of products list
     * and filter by discount percentage
     *
     * @return void
     */
    public function test_get_list_products_by_discount_percentage()
    {
        $discount_id = Discount::where('percentage', 15)->first()->id;
        $model = Product::where('discount_id', $discount_id)->first();

        $response = $this->json('GET', '/api/products?page=1&size=10&discount=15');

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $model->id,
            'price' => $model->price
        ]);
    }

    /**
     * Test getting of products list
     * and filter by priceLess
     *
     * @return void
     */
    public function test_get_list_products_by_priceLess()
    {
        $model = Product::where('price', '<=', 21000)->first();

        $response = $this->json('GET', '/api/products?page=1&size=10&priceLess=21000');

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $model->id,
            'price' => $model->price
        ]);
    }

    /**
     * Test getting of product with discount by id
     * and assert format of response
     *
     * @return void
     */
    public function test_get_product_by_id()
    {
        $model = Product::whereHas('discount')->first();

        $response = $this->json('GET', "/api/products/{$model->id}");

        $response->assertOk();

        $response->assertJsonStructure([
           'id',
           'sku',
           'name',
           'category',
           'price' => [
               'original',
               'final',
               'discount_percentage',
               'currency'
           ]
        ]);

        $response->assertJsonFragment([
            'sku' => $model->sku,
            'name' => $model->name,
            'category' => $model->category
        ]);

        // remove sign % from response
        $discount_percentage = substr_replace($response->json('price')['discount_percentage'] ,'', -1);

        $final = $model->price - (($model->price * $discount_percentage) / 100);

        $this->assertEquals($model->price, $response->json('price')['original']);
        $this->assertEquals($final, $response->json('price')['final']);
        $this->assertEquals($model->discount->percentage, $discount_percentage);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }

    /**
     * Test getting of product without discount by id
     * and assert format of response
     *
     * @return void
     */
    public function test_get_product_by_id_without_discount()
    {
        $model = Product::whereNull('discount_id')->first();

        $response = $this->json('GET', "/api/products/{$model->id}");

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'sku',
            'name',
            'category',
            'price' => [
                'original',
                'final',
                'discount_percentage',
                'currency'
            ]
        ]);

        $response->assertJsonFragment([
            'sku' => $model->sku,
            'name' => $model->name,
            'category' => $model->category
        ]);

        $this->assertEquals($model->price, $response->json('price')['original']);
        $this->assertEquals($model->price, $response->json('price')['final']);
        $this->assertEquals(NULL, $response->json('price')['discount_percentage']);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }


    /**
     * Create product
     *
     * @return void
     */
    public function test_create_product()
    {
        $request = [
            'sku' => '000001',
            'name' => 'Black T-shirt',
            'category' => 'T-shirt',
            'price' => 120.00
        ];

        $response = $this->json('POST', '/api/products', $request);

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'sku' => $request['sku'],
            'name' => $request['name'],
            'category' => $request['category']
        ]);

        // remove sign % from response
        $discount_percentage = substr_replace($response->json('price')['discount_percentage'] ,'', -1);

        $this->assertIsArray($response->json('price'));

        // when creating product, price value will be formatted to integer. In this case 120.00 to 12000
        $this->assertEquals($request['price'] * 100, $response->json('price')['original']);
        $this->assertEquals($request['price'] * 100, $response->json('price')['final']);
        $this->assertEquals(NULL, $response->json('price')['discount_percentage']);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }


    /**
     * Create product
     * and set 15% discount when sku = 000003
     *
     * @return void
     */
    public function test_create_product_with_sku()
    {
        $request = [
            'sku' => '000003',
            'name' => 'Black T-shirt',
            'category' => 'T-shirt',
            'price' => 650.00
        ];

        $response = $this->json('POST', '/api/products', $request);

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'sku' => $request['sku'],
            'name' => $request['name'],
            'category' => $request['category']
        ]);

        // remove sign % from response
        $discount_percentage = substr_replace($response->json('price')['discount_percentage'] ,'', -1);

        $this->assertIsArray($response->json('price'));

        // when creating product, price value will be formatted to integer. In this case 650.00 to 65000
        $this->assertEquals($request['price'] * 100, $response->json('price')['original']);

        // Here sku = 000003 so for the product has been applied (automatically) 15% discount
        $this->assertEquals(15, $discount_percentage);

        $final = $response->json('price')['original'] - (($response->json('price')['original'] * 15) / 100);
        $this->assertEquals($final, $response->json('price')['final']);
        $this->assertEquals('EUR', $response->json('price')['currency']);

    }

    /**
     * Create product
     * and set 30% discount when category = boots
     * @return void
     */
    public function test_create_product_with_category()
    {
        $request = [
            'sku' => '000007',
            'name' => 'Black Nike Boots',
            'category' => 'boots',
            'price' => 700.00
        ];

        $response = $this->json('POST', '/api/products', $request);

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'sku' => $request['sku'],
            'name' => $request['name'],
            'category' => $request['category']
        ]);

        // remove sign % from response
        $discount_percentage = substr_replace($response->json('price')['discount_percentage'] ,'', -1);

        $this->assertIsArray($response->json('price'));

        // when creating product, price value will be formatted to integer. In this case 700.00 to 70000
        $this->assertEquals($request['price'] * 100, $response->json('price')['original']);

        // Here category = boots so for the product has been applied (automatically) 30% discount
        $this->assertEquals(30, $discount_percentage);

        $final = $response->json('price')['original'] - (($response->json('price')['original'] * 30) / 100);
        $this->assertEquals($final, $response->json('price')['final']);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }

    /**
     * Create product with both condition (sku and category)
     * and set the biggest (30%) discount
     *
     * @return void
     */
    public function test_create_product_with_both()
    {
        $request = [
            'sku' => '000003',
            'name' => 'Black Nike Boots',
            'category' => 'boots',
            'price' => 700.00
        ];

        $response = $this->json('POST', '/api/products', $request);

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'sku' => $request['sku'],
            'name' => $request['name'],
            'category' => $request['category']
        ]);

        // remove sign % from response
        $discount_percentage = substr_replace($response->json('price')['discount_percentage'] ,'', -1);

        $this->assertIsArray($response->json('price'));

        // when creating product, price value will be formatted to integer. In this case 700.00 to 70000
        $this->assertEquals($request['price'] * 100, $response->json('price')['original']);

        // Here category = boots (30%) and sku = 000003 (15%)
        // so for the product has been applied (automatically) the biggest (30%) discount
        $this->assertEquals(30, $discount_percentage);

        $final = $response->json('price')['original'] - (($response->json('price')['original'] * 30) / 100);
        $this->assertEquals($final, $response->json('price')['final']);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }

    /**
     * Test validation
     *
     * @return void
     */
    public function test_validation_create_product()
    {
        $newData = [
            'sku' => 00003,
            'name' => 123,
            'discount_id' => 99999
        ];

        $response = $this->json('POST', "/api/products", $newData);

        $response->assertInvalid(['sku', 'name', 'category', 'discount_id']);
    }

    /**
     * Update product
     * and apply discount manually
     *
     * @return void
     */
    public function test_update_product()
    {
        $model = Product::whereNull('discount_id')->first();
        $discount = Discount::first();

        $newData = [
            'name' => 'New product name',
            'price' => 900.00,
            'discount_id' => $discount->id
        ];

        $response = $this->json('PUT', "/api/products/{$model->id}", $newData);

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'sku',
            'name',
            'category',
            'price' => [
                'original',
                'final',
                'discount_percentage',
                'currency'
            ]
        ]);

        // reformat 900.00 to 90000
        $final = ($newData['price'] * 100) - ((($newData['price'] * 100) * $discount->percentage) / 100);

        $this->assertEquals($newData['price'] * 100, $response->json('price')['original']);
        $this->assertEquals($final, $response->json('price')['final']);
        $this->assertEquals($discount->percentage.'%', $response->json('price')['discount_percentage']);
        $this->assertEquals('EUR', $response->json('price')['currency']);
    }

    /**
     * Update product
     * and apply discount automatically
     *
     * @return void
     */
    public function test_update_product_apply_discount_automatically()
    {
        $model = Product::whereNull('discount_id')->first();

        $newData = [
            'price' => 750.00,
            'category' => 'boots'
        ];

        $response = $this->json('PUT', "/api/products/{$model->id}", $newData);

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'sku',
            'name',
            'category',
            'price' => [
                'original',
                'final',
                'discount_percentage',
                'currency'
            ]
        ]);

        // apply 30% discount
        $this->assertEquals('30%', $response->json('price')['discount_percentage']);
    }

    /**
     * Test validation
     *
     * @return void
     */
    public function test_validation_update_product()
    {
        $model = Product::first();

        $newData = [
            'sku' => 00003,
            'name' => 123,
            'discount_id' => 99999
        ];

        $response = $this->json('PUT', "/api/products/{$model->id}", $newData);

        $response->assertInvalid(['sku', 'name', 'discount_id']);
    }

    /**
     * Test update of product not exists
     *
     * @return void
     */
    public function test_update_product_not_exists()
    {
        $response = $this->json('DELETE', "/api/products/9999999");

        $response->assertStatus(404);
    }

    /**
     * Test deleting of product
     *
     * @return void
     */
    public function test_delete_product()
    {
        $model = Product::first();

        $response = $this->json('DELETE', "/api/products/{$model->id}");

        $this->assertModelMissing($model);
    }

    /**
     * Test delete of product not exists
     *
     * @return void
     */
    public function test_delete_product_not_exists()
    {
        $response = $this->json('DELETE', "/api/products/9999999");

        $response->assertStatus(404);
    }

}
