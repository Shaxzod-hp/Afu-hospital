<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->index('is_published');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->index('specialization_id');
            $table->index('experience_years');
        });

        Schema::table('surgeries', function (Blueprint $table) {
            $table->index('price');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('statsionar_packages', function (Blueprint $table) {
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex(['specialization_id']);
            $table->dropIndex(['experience_years']);
        });

        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropIndex(['price']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('statsionar_packages', function (Blueprint $table) {
            $table->dropIndex(['price']);
        });
    }
};