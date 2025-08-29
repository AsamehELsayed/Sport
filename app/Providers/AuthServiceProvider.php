<?php

namespace App\Providers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Policies\EmailCampaignPolicy;
use App\Policies\EmailTemplatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        EmailTemplate::class => EmailTemplatePolicy::class,
        EmailCampaign::class => EmailCampaignPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
