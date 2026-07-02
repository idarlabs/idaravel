<?php

namespace Idaravel\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use Idaravel\Form\FormBuilder as IdarForm;

class Idaravel extends ServiceProvider {
    
    public function register(){
        $this->app->register(Modular::class);
    }

    public function boot()
    {
        Blade::component('idar-form', IdarForm::class);
    }
}