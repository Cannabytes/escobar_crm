<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $credentials = DB::table('company_credentials')->get();

        foreach ($credentials as $credential) {
            $banks = DB::table('banks')
                ->where('company_id', $credential->company_id)
                ->get(['id']);

            foreach ($banks as $bank) {
                $update = array_filter([
                    'login' => $credential->login,
                    'login_id' => $credential->login_id,
                    'password' => $credential->password,
                    'email' => $credential->email,
                    'email_password' => $credential->email_password,
                    'online_banking_url' => $credential->online_banking_url,
                    'manager_name' => $credential->manager_name,
                    'manager_phone' => $credential->manager_phone,
                ], static fn ($value) => ! is_null($value));

                if (empty($update)) {
                    continue;
                }

                DB::table('banks')
                    ->where('id', $bank->id)
                    ->whereNull('login')
                    ->update($update);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ничего не откатываем, чтобы не потерять данные, уже привязанные к банкам
    }
};




