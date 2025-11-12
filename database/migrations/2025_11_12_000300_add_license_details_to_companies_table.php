<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('license_number', 191)->nullable()->after('license_file');
            $table->string('registration_number', 191)->nullable()->after('license_number');
            $table->date('incorporation_date')->nullable()->after('registration_number');
            $table->date('expiry_date')->nullable()->after('incorporation_date');
            $table->string('free_zone', 191)->nullable()->after('expiry_date');
            $table->text('business_activities')->nullable()->after('free_zone');
            $table->text('legal_address')->nullable()->after('business_activities');
            $table->text('actual_address')->nullable()->after('legal_address');
            $table->string('owner_name', 191)->nullable()->after('actual_address');
            $table->string('owner_email', 191)->nullable()->after('owner_name');
            $table->string('owner_phone', 100)->nullable()->after('owner_email');
            $table->string('owner_website', 255)->nullable()->after('owner_phone');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'license_number',
                'registration_number',
                'incorporation_date',
                'expiry_date',
                'free_zone',
                'business_activities',
                'legal_address',
                'actual_address',
                'owner_name',
                'owner_email',
                'owner_phone',
                'owner_website',
            ]);
        });
    }
};

