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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Настройки шаблона
            $table->string('theme', 20)->default('light'); // light, dark, system
            $table->string('style', 20)->default('light'); // light, dark, bordered, etc.
            $table->string('layout_type', 20)->default('vertical'); // vertical, horizontal
            $table->string('navbar_type', 20)->default('fixed'); // fixed, static, hidden
            $table->string('footer_type', 20)->default('fixed'); // fixed, static, hidden
            $table->boolean('layout_navbar_fixed')->default(true);
            $table->boolean('show_dropdown_on_hover')->default(true);
            
            // Язык интерфейса
            $table->string('language', 10)->default('en'); // en, ru
            
            // Прочие настройки
            $table->json('custom_settings')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
