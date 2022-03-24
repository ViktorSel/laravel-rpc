<?php


namespace IceCloud\RpcServer\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeProcedure extends Command
{
    const RPC_PATH = 'Rpc/Procedures';

    protected $signature = 'rpc:make:procedure {name}';

    public function handle() {
        $filesystem = new Filesystem();
        $name = $this->argument('name');

        $newName = [];
        foreach (explode('.', $name) as $items) {
            $newName[] = Str::studly($items);
        }

        $name = implode('.', $newName);

        $data = explode('.', $name);
        $class = array_pop($data);

        $directory = config('rpc-server.proceduresPath') .
            DIRECTORY_SEPARATOR .
            implode(DIRECTORY_SEPARATOR, $data);


        if(!$filesystem->isDirectory($directory)) {
            $filesystem->makeDirectory(
                $directory,
                0755,
                true,
                true
            );
        }

        $namespace = "App\\Rpc\\Procedures\\" . implode("\\", $data);
        $content = view("rpc-server::procedure", [
            "class" => $class,
            "name" => $name,
            "namespace" => rtrim($namespace, '\\')
        ]);


        $filesystem->put(
            $directory . DIRECTORY_SEPARATOR . $class .'.php',
            "<?php " . "\n" . $content->render()
        );

        return 0;
    }
}
