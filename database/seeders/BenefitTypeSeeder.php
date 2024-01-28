<?php

namespace Database\Seeders;

use App\Models\BenefitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BenefitTypeSeeder extends Seeder
{
    protected array $entries = [
        "Income based Jobseekers Allowance (JSA)",
        "Income Based Employment & Support Allowance (ESA)",
        "Income Support",
        "Pension Credit Guaranteed Credit",
        "Working Tax Credit",
        "Child Tax Credit",
        "Universal Credit",
        "Pension Credit Savings Credit",
        "Child Benefit",
        "Housing Benefit"
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $benefitType) {
            BenefitType::firstOrCreate([
                'name' => $benefitType
            ]);
        }
    }
}
