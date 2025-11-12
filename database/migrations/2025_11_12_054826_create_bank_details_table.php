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
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->string('detail_type', 50); // account_number, iban, swift, recipient_name тощо
            $table->string('detail_key', 100); // Ключ/назва реквізиту
            $table->text('detail_value'); // Значення реквізиту
            $table->string('currency', 10)->nullable(); // Валюта (для рахунків)
            $table->text('notes')->nullable(); // Додаткові нотатки
            $table->boolean('is_primary')->default(false); // Основний реквізит?
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('bank_id');
            $table->index('detail_type');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
