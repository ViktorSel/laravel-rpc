<?php

namespace IceCloud\RpcServer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Cleanup extends Command
{
    protected $signature = "rpc:cleanup";

    public function handle()
    {
        $fs = new Filesystem();
        $path = config('rpc-server.schemaCachePath');

        $filenames = [];
        foreach ($fs->files($path) as $file) {
            if ($file->getExtension() === 'php') {
                $filenames[]= $file->getPathname();
            }
        }

        $fs->delete($filenames);

        $this->info("Ok");
    }
}
