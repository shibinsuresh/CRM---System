<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Store short aliases in notes.notable_type instead of full class names.
        Relation::morphMap([
            'contact' => \App\Models\Contact::class,
            'company' => \App\Models\Company::class,
            'deal' => \App\Models\Deal::class,
        ]);
    }
}
