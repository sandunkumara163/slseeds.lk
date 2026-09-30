<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@slseeds.lk',
            'role' => 'admin',
            'password' => bcrypt('password'), // password
            'phone' => '0712345678',
        ]);

        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@slseeds.lk',
            'role' => 'customer',
            'password' => bcrypt('password'), // password
            'phone' => '0771234567',
        ]);
    }
}
