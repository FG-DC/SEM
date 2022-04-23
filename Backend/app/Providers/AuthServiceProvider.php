<?php

namespace App\Providers;

use App\Models\Alert;
use App\Models\Consumption;
use App\Models\Division;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Observation;
use App\Models\TrainingExample;
use App\Models\User;
use App\Policies\ConsumptionPolicy;
use App\Policies\DivisionPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\EquipmentTypePolicy;
use App\Policies\ObservationPolicy;
use App\Policies\TrainingExamplePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        EquipmentType::class => EquipmentTypePolicy::class,
        Consumption::class => ConsumptionPolicy::class,
        Division::class => DivisionPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        Observation::class => ObservationPolicy::class,
        TrainingExample::class => TrainingExamplePolicy::class,
        Alert::class => Alert::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
    }
}
