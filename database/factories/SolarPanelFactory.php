<?php

namespace Database\Factories;

use App\Models\Carbon;
use App\Models\SolarPanel;
use App\Models\User;
use App\Services\CarbonCalculator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SolarPanel>
 */
class SolarPanelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'performance' => $this->faker->randomFloat(2, 70, 100),
            'energy_produced' => $this->faker->randomFloat(2, 5, 30),
            'energy_consumed' => $this->faker->randomFloat(2, 2, 25),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (SolarPanel $solarPanel) {
            $emissionFactor = 0.5;

            $co2Saved = CarbonCalculator::calculateCO2Saved(
                $solarPanel->energy_produced,
                $emissionFactor
            );

            $equivalentTrees = round(CarbonCalculator::calculateEquivalentTreesPlanted($co2Saved),2);

            Carbon::create([
                'solar_panel_id' => $solarPanel->id,
                'co2_saved' => $co2Saved,
                'equivalent_trees' => $equivalentTrees,
            ]);
        });
    }
}
