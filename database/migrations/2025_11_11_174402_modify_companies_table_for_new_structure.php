<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('companies');
        
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('country', 100);
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('license_file', 255)->nullable();
            $table->timestamps();
            
            $table->index('moderator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
