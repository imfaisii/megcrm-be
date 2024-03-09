<?php

namespace Database\Seeders;

use App\Enums\Permissions\RoleEnum;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TeamRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {

            $teamAdmin = Role::firstOrCreate([
                'name' => RoleEnum::TEAM_ADMIN,
            ], [
                'guard_name' => 'sanctum',

            ]);
            $teamMember = Role::firstOrCreate([
                'name' => RoleEnum::TEAM_MEMBER,
            ], [
                'guard_name' => 'sanctum',
            ]);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
