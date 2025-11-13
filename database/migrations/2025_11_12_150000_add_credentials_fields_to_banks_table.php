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
        Schema::table('banks', function (Blueprint $table) {
            $table->string('login', 191)->nullable()->after('notes');
            $table->string('login_id', 191)->nullable()->after('login');
            $table->text('password')->nullable()->after('login_id');
            $table->string('email', 191)->nullable()->after('password');
            $table->text('email_password')->nullable()->after('email');
            $table->string('online_banking_url', 500)->nullable()->after('email_password');
            $table->string('manager_name', 191)->nullable()->after('online_banking_url');
            $table->string('manager_phone', 100)->nullable()->after('manager_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropColumn([
                'login',
                'login_id',
                'password',
                'email',
                'email_password',
                'online_banking_url',
                'manager_name',
                'manager_phone',
            ]);
        });
    }
};




