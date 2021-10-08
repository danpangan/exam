<?php

namespace Tests\Unit;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_new_user() {

        $response = $this->postJson('api/register', [
            'name' => 'backend',
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
            'password_confirmation' => 'test123'
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'User successfully registered'
        ]);
    }

    public function test_register_duplicate_user_email() {

        $this->seed(UserSeeder::class);

        $response = $this->postJson('api/register', [
            'name' => 'backend',
            'email' => 'backend@multisyscorp.com',
            'password' => 'test123',
            'password_confirmation' => 'test123'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Email already taken'
        ]);
    }
}
