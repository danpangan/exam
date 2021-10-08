<?php

namespace Tests\Unit;

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $product_id;
    protected $qty;

    public function test_order() {

        $this->seed(ProductSeeder::class);
        $this->seed(UserSeeder::class);

        $this->product_id = 1;
        $this->qty = 2;

        $loginResponse = $this->postJson('api/login', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $token = $loginResponse->json()['access_token'];

        $product = Product::find($this->product_id);
        $oldStock = $product->available_stock;

        $response = $this->postJson('api/order', [
            'product_id' => $this->product_id,
            'quantity' => $this->qty
        ],['HTTP_Authorization' => 'Bearer' . $token]);

        $product = Product::find($this->product_id);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'You have successfully ordered this product.'
        ]);

        $this->assertEquals($oldStock, ($product->available_stock + $this->qty));
    }

    public function test_insufficient_stock() {

        $this->seed(ProductSeeder::class);
        $this->seed(UserSeeder::class);


        $this->product_id = 2;
        $this->qty = 99;

        $loginResponse = $this->postJson('api/login', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $token = $loginResponse->json()['access_token'];

        $product = Product::find($this->product_id);

        $response = $this->postJson('api/order', [
            'product_id' => $this->product_id,
            'quantity' => $this->qty
        ],['HTTP_Authorization' => 'Bearer' . $token]);

        $product = Product::find($this->product_id);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Failed to order this product due to unavailability of the stock'
        ]);

        $this->assertTrue($product->available_stock, '<', $this->qty);
    }
}
