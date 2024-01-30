<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'first_name' => fake()->firstName,
            'middle_name' => fake()->lastName,
            'last_name' => fake()->lastName,
            'email' => fake()->email,
            'phone_no' => fake()->phoneNumber,
            'dob' => fake()->date,
            'post_code' => fake()->postcode,
            'address' => fake()->address,
            'is_marked_as_job' => fake()->boolean, // Generates a random true or false
            'created_by_id' => 1,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Lead $lead) {
            //! AUTH REQUIRED BY STATUS PACKAGE
            Auth::loginUsingId(1);

            $lead->setStatus(LeadStatus::inRandomOrder()->first()->name, 'Factory Seeded');
        });
    }
}
