<?php

namespace Database\Seeders;

use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SolarPanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->has(
                SolarPanel::factory()->count(5)
            )
            ->create();
    }
}
