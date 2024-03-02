<?php

namespace Database\Seeders;

use App\Enums\Permissions\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => app()->environment('local') ? 'cfaysal099@gmail.com' : 'megcrm24@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => 'Megnweg123789!',
            'created_by_id' => 1,
        ])->assignRole(Role::where('name', RoleEnum::SUPER_ADMIN)->first());
    }
}
