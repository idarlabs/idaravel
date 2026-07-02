<?php

namespace Idaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveModuleCommand extends Command {
    protected $signature = 'remove:module {name}';
    protected $description = 'Remove an existing Idaravel module';

    public function handle(){
        $name = $this->argument('name');
        $modulePath = app_path("Modules/{$name}");

        if(!File::exists($modulePath)){
            $this->error("Modul {$name} tidak ditemukan di folder app/Modules/!");
            return;
        }

        if ($this->confirm("Apakah kamu yakin ingin MENGHAPUS TOTAL modul {$name}? Tindakan ini tidak bisa dibatalkan!")) {
            File::deleteDirectory($modulePath);
            $this->info("Module {$name} beserta seluruh filenya berhasil dihapus!");
        } else {
            $this->comment("Penghapusan modul {$name} dibatalkan.");
        }
    }
}