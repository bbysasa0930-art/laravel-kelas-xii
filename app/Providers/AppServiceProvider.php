<?php

namespace App\Providers;

use App\Models\Genre;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        // Atur lokal Carbon ke Bahasa Indonesia untuk format tanggal yang mudah dibaca
        \Carbon\Carbon::setLocale('id');

        if (Schema::hasTable('genres')) {
            view()->share('genres', Genre::all());
        }
    }
}
