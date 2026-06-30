<?php

namespace App\Providers;

use App\Models\TicketTier;
use App\Policies\TicketTierPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        TicketTier::class => TicketTierPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
