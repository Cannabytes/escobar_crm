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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name', 191); // Назва банку (Монобанк, ПриватБанк тощо)
            $table->string('country', 100)->nullable(); // Країна банку
            $table->string('bank_code', 50)->nullable(); // Код банку (MFI code, SWIFT тощо)
            $table->text('notes')->nullable(); // Додаткові нотатки
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('company_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
