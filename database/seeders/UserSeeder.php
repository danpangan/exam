<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'email' => 'backend@multisyscorp.com',
            'name' => 'backed',
            'password' => bcrypt('test123'),
        ];

        $user = User::where('email', '=', $data['email'])->first();
        if(is_null($user)) User::create($data);
    }
}
