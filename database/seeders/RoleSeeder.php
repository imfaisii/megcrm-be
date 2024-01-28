<?php

namespace Database\Seeders;

use App\Enums\Permissions\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = RoleEnum::getValues();

        foreach ($roles as $key => $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
