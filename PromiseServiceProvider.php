<?php


namespace App\Packages\Promise;


use Illuminate\Support\ServiceProvider;

class PromiseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Promise', function () {
            return $this->app->make( Promise::class);
        });
    }

}
