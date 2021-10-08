<?php

namespace Tests\Unit;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login() {

        $this->seed(UserSeeder::class);

        $response = $this->postJson('api/login', [
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token'
        ]);
    }

    public function test_user_invalid_login() {

        $this->seed(UserSeeder::class);

        $response = $this->postJson('api/login', [
            'email' => 'backend123123@multisyscorp.com',
            'password' => 'test121231233',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);
    }
}
