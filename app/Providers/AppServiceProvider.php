<?php

namespace App\Providers;

use App\Models\Province;
use App\Models\SilcoinGiftset;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

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

        // if(Schema::hasTable('provinces')) {
        //     $province = Province::all()->pluck("name", "id");
        //     if ($province) {
        //         view()->share('provinces', $province);
        //     }
        // }

	    if ( env('REDIRECT_HTTPS') ) {
		    \URL::forceScheme('https');
        }
        Paginator::useBootstrap();
    }
}
