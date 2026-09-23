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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Yangilik sarlavhasi
            $table->string('slug')->unique(); // URL uchun (masalan: yangi-shifoxona-ochildi)
            $table->foreignId('category_id')->nullable()->nullOnDelete();
            $table->text('summary')->nullable(); // Qisqacha tavsif
            $table->longText('content'); // To'liq matn
            $table->string('image')->nullable(); // Asosiy rasm yo'li
            $table->unsignedBigInteger('views')->default(0); // Ko'rishlar soni
            $table->boolean('is_published')->default(true); // E'lon qilinganligi
            $table->timestamp('published_at')->nullable(); // Nashr etilgan sana
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
