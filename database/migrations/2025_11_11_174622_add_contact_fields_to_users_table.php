<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 64)->nullable()->after('email');
            $table->string('telegram', 100)->nullable()->after('phone');
            $table->string('whatsapp', 64)->nullable()->after('telegram');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'telegram', 'whatsapp']);
        });
    }
};
