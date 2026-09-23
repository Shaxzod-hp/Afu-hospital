<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('slug')->unique();
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('position')->nullable();
            $table->unsignedInteger('experience_years')->nullable();
            $table->text('bio')->nullable();
            $table->text('education')->nullable();
            $table->text('previous_workplace')->nullable();
            $table->string('phone')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('instagram')->nullable();
            $table->string('telegram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};