<?php

namespace Idarlabs\Idaravel;

use Illuminate\Support\ServiceProvider;

class IdaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Tempat register binding atau config
    }

    public function boot()
    {
        // Tempat load routes, views, atau migrations nanti
    }
}
