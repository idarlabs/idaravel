<?php

namespace Idaravel\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command {
  protected $signature = 'make:module {name}';
  protected $description = 'Generate module Idaravel';

  public function handle(){
    $name = $this->argument('name');
    $modulePath = app_path("Modules/{$name}");

    if(File::exists($modulePath)){
      $this->error("Duplikat Modul {$name}!");
      return;
    }

    File::makeDirectory("{$modulePath}/Backend", 0755, true);
    File::makeDirectory("{$modulePath}/Frontend", 0755, true);
    File::makeDirectory("{$modulePath}/routes", 0755, true);

    File::put("{$modulePath}/Backend/DefaultController.php",
      $this->getStub('controller', ['module' => $name, 'module_lower' => strtolower($name)])
    );

    File::put("{$modulePath}/Frontend/index.blade.php",
      $this->getStub('view', ['module' => $name])
    );

    File::put("{$modulePath}/routes/web.php",
      $this->getStub('routes', ['module' => $name, 'module_lower' => strtolower($name)])
    );

    $this->info("Module {$name} berhasil dibuat!");
  }

  private function getStub($filename, $replacements = []){
    $stubPath = __DIR__ . "/../idarstub/{$filename}.stub";

    if(!File::exists($stubPath)){
      $this->error("Stub file {$filename}.stub tidak ditemukan!");
      return '';
    }

    $stub = File::get($stubPath);

    foreach($replacements as $key => $value){
      $stub = str_replace("{{{$key}}}", $value, $stub);
    }

    return $stub;
  }
}
