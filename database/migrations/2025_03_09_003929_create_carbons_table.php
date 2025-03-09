<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carbons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_panel_id')->constrained()->onDelete('cascade');
            $table->float('co2_saved');
            $table->float('equivalent_trees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbons');
    }
};
