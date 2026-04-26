<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            AcademicYear::create([
                'year_number' => $i,
                'label'       => "Year $i"
            ]);
        }
    }
}
