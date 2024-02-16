<?php

namespace Database\Seeders;

use App\Enums\Permissions\RoleEnum;
use App\Models\Surveyor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SurveyorSeeder extends Seeder
{
    protected int $total = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < $this->total; $i++) {
            $name = "Surveyor $i";
            $email = str_replace(" ", "", str()->lower($name)) . "@megcrm.co.uk";
            $password = "12345678";

            $user = User::firstOrCreate(
                [
                    'name' => $name,
                    'email' => $email
                ],
                [
                    'email_verified_at' => now(),
                    'password' => $password,
                    'created_by_id' => 1
                ]
            )->assignRole(Role::where('name', RoleEnum::SURVEYOR)->first());

            $user->surveyor()->firstOrCreate([
                'created_by_id' => 1
            ]);
        }
    }
}
