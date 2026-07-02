<?php

namespace Idaravel\Commands;

use Illuminate\Console\Command;

class PublishLayoutCommand extends Command {
    protected $signature = 'idaravel:publish {--force : Overwrite existing files}';
    protected $description = 'Publish Idaravel layout templates and assets';

    public function handle(){
        $this->info('Memulai proses publish layout Idaravel...');

        $arguments = [
            '--tag' => 'idaravel-layout'
        ];

        if ($this->option('force')) {
            $arguments['--force'] = true;
        }

        $this->call('vendor:publish', $arguments);
        $this->info('Layout Idaravel berhasil dipublish!');
    }
}