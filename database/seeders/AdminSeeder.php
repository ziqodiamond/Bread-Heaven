<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin User
        |--------------------------------------------------------------------------
        | Create super admin account dengan credentials yang diberikan
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(
            ['email' => 'hadziqfurqon1508@gmail.com'],
            [
                'name' => 'Hadziq Furqon',
                'phone' => '089663764423',
                'password' => 'rajamolen',
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
