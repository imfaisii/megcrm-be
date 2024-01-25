<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'megcrm24@gmail.com'
        ], [
            'name' => 'Super Admin',
            'password' => 'Megnweg123789!'
        ]);
    }
}
