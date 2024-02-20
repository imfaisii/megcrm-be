<?php

namespace Database\Seeders;

use App\Enums\Permissions\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class SurveyorSeeder extends Seeder
{
    protected int $total = 2;

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
            )->assignRole(RoleEnum::SURVEYOR);
        }
    }
}
