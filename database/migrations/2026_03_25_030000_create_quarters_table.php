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
        Schema::create('quarters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Jul-Sept 2025"
            $table->string('code'); // e.g., "Q1"
            $table->integer('quarter_number'); // 1, 2, 3, 4
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            
            $table->unique(['fiscal_year_id', 'quarter_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarters');
    }
};