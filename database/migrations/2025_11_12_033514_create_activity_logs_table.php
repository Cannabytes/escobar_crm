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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // Пользователь, который выполнил действие
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Тип действия: create, update, delete, login, logout, view, etc.
            $table->string('action', 50)->index();
            
            // Модель, с которой было взаимодействие (Company, User, etc.)
            $table->string('model_type')->nullable()->index();
            $table->unsignedBigInteger('model_id')->nullable()->index();
            
            // Описание действия
            $table->text('description')->nullable();
            
            // Данные до и после изменения (для update)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // IP адрес и User Agent
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // URL запроса
            $table->string('url', 500)->nullable();
            
            // HTTP метод (GET, POST, PUT, DELETE)
            $table->string('http_method', 10)->nullable();
            
            // Уровень важности: info, warning, error, critical
            $table->string('level', 20)->default('info')->index();
            
            // Дополнительные метаданные
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
