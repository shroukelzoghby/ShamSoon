<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolarPanel extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'performance',
        'energy_produced',
        'energy_consumed'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function carbon()
    {
        return $this->hasOne(Carbon::class);
    }
}
