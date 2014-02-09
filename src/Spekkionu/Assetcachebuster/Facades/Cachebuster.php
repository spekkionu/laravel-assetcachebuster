<?php namespace Spekkionu\Assetcachebuster\Facades;

use Illuminate\Support\Facades\Facade;

class Cachebuster extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'assetcachebuster';
    }
}
