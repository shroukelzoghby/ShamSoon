<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carbon extends Model
{
    protected $fillable = ['solar_panel_id', 'co2_saved', 'equivalent_trees'];

    public function solarPanel()
    {
        return $this->belongsTo(SolarPanel::class);
    }
}
