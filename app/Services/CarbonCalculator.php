<?php

namespace App\Services;

class CarbonCalculator
{
    public static function calculateCO2Saved($energyProduced, $emissionFactor)
    {
        return $energyProduced * $emissionFactor;
    }

    public static function calculateEquivalentTreesPlanted($co2Saved)
    {
        return round($co2Saved / 22, 4);
    }

}
