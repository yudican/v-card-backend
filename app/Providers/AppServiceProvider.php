<?php

namespace App\Providers;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        if (Schema::hasTable('teams')) {
            $team = optional(Team::find(1));
            View::share(['curteam' => $team]);
            Schema::defaultStringLength(191);

            config(['app.locale' => 'id']);
            Carbon::setLocale('id');
            date_default_timezone_set('Asia/Jakarta');
        }
    }
}
