<?php


namespace App\Packages\Promise;


use Illuminate\Support\Facades\Facade;

class PromiseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Promise';
    }
}
