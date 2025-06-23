<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\BenefitDetail;
use Illuminate\Database\Seeder;

class BenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Benefits
        $firstBenefit = Benefit::create(['name' => 'First Benefit']);

        // Benefit Details
        $firstBenefitDetail = BenefitDetail::create(['name' => '2 Hours', 'time_hours' => 2]);

        // Benefit Relationship
        $firstBenefit->benefit_detail()->attach($firstBenefitDetail);
    }
}
