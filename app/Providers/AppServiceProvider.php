<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $settings = null;
            if ($settings === null) {
                try {
                    if (Schema::hasTable('settings')) {
                        $settings = Setting::first();
                    }
                } catch (\Throwable $e) {
                    $settings = null;
                }
            }
            $view->with('appSettings', $settings);
        });
    }
}
