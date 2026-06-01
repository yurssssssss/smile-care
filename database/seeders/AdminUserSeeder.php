<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
       User::updateOrCreate([
    'email' => 'admin@gmail.com',
], [
    'first_name' => 'Admin',  
    'last_name'  => 'User',     
    'email'      => 'admin@gmail.com',
    'password'   => bcrypt('admin123'),
    'role'       => 'admin',
    'created_at' => now(),
    'updated_at' => now(),
]);
    }
}