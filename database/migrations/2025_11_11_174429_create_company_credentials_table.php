<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('login', 191)->nullable();
            $table->string('login_id', 191)->nullable();
            $table->text('password')->nullable();
            $table->string('email', 191)->nullable();
            $table->text('email_password')->nullable();
            $table->string('online_banking_url', 500)->nullable();
            $table->string('manager_name', 191)->nullable();
            $table->string('manager_phone', 64)->nullable();
            $table->timestamps();
            
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_credentials');
    }
};
