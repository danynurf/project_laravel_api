<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@xmpl.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'email' => 'seller@xmpl.com',
            'password' => Hash::make('seller123'),
            'role' => 'seller'
        ]);
    }
}
