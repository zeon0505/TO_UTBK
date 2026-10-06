<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('dosen', function (User $user) {
            return $user->isDosen();
        });
    }
}
