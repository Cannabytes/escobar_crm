<?php

namespace App\Providers;

use App\Models\Bank;
use App\Models\Company;
use App\Policies\BankPolicy;
use App\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Bank::class => BankPolicy::class,
    ];
}
