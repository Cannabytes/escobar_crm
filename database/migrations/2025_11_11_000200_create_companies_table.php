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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('license_number', 191)->unique();
            $table->string('registration_number', 191)->unique();
            $table->date('incorporation_date');
            $table->date('expiration_date');
            $table->string('jurisdiction_zone', 191);
            $table->text('business_activities');
            $table->string('legal_address', 255);
            $table->string('factual_address', 255);
            $table->string('owner_name', 191);
            $table->string('email', 191);
            $table->string('phone', 64);
            $table->string('website', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

