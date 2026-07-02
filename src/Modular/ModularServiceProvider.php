<?php

namespace Idaravel\Modular;

use Idaravel\Modular\Commands\MakeModuleCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ModularServiceProvider extends ServiceProvider {

  public function register(){
    $this->commands([ MakeModuleCommand::class ]);
  }

  public function boot(){
    $this->loadModuleRoutes();
    $this->loadModuleViews();

    View::share('idar', new \Idaravel\AllPack\Idar());

    $this->publishes([
        __DIR__ . '/template/idaravel' => resource_path('views/idaravel'),
        __DIR__ . '/template/partials' => resource_path('views/partials'),
        __DIR__ . '/template/public' => public_path(),
    ], 'idaravel-layout');
  }

  private function loadModuleRoutes(){
    $modulesPath = app_path('Modules');

    if(File::exists($modulesPath)){
      foreach (glob($modulesPath . '/*/routes/web.php') as $route){
        Route::middleware('web')->group(function() use($route){
          require $route;
        });
      }
    }
  }
  private function loadModuleViews(){
    $modulesPath = app_path('Modules');

    if(File::exists($modulesPath)){
      $modules = File::directories($modulesPath);

      foreach($modules as $module){
        $moduleName = basename($module);
        $viewPath = $module . '/Frontend';

        if(File::exists($viewPath)){
          View::addNamespace(strtolower($moduleName), $viewPath);
        }
      }
    }
  }
}
