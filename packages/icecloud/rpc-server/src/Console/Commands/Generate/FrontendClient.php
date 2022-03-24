<?php


namespace IceCloud\RpcServer\Console\Commands\Generate;


use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\ServerVersionStorage;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class FrontendClient extends Command
{
    const NPM_PACKAGE_NAME_TEMPLATE = 'ice-lib-rpc-{project}-{scope}-client';

    const DEFAULT_NPM_PACKAGE_VERSION = '1.0.0';

    const OUTPUT_DIR_ARGUMENT = 'output_dir';
    const PROJECT_ARGUMENT = 'project';
    const SCOPE_ARGUMENT = 'scope';
    const ONLY_OPTION = 'only';

    const IS_MINOR_OPTION = 'minor';

    protected $signature = "rpc:make:client:frontend
        {class : Класс сервера}
        {project : Слаг проекта}
        {scope? : Скоуп проекта (backend, cpi, api, etc)}
        {--only= : Только маски методов}
        {--M|minor : Сгенерировать минорную версию пакета}";

    protected function getOutputDirArgument()
    {
        return $this->argument(self::OUTPUT_DIR_ARGUMENT);
    }

    protected function getProjectArgument()
    {
        return $this->argument(self::PROJECT_ARGUMENT);
    }

    protected function getScopeArgument()
    {
        return $this->argument(self::SCOPE_ARGUMENT);
    }

    protected function getOnlyOption(): ?array
    {
        $val=$this->option(self::ONLY_OPTION);
        return empty($val) ? null : explode(',', $val);
    }

    protected function getRegistryUrl()
    {
        return 'http://verdaccio.icecorp.ru';
    }

    public function isMinorOption()
    {
        return $this->option(self::IS_MINOR_OPTION);//option(self::IS_MINOR_OPTION);
    }

    protected function makeNpmPackageName()
    {
        return str_replace([
            '{project}',
            '{scope}'
        ], [
            $this->getProjectArgument(),
            $this->getScopeArgument()
        ], self::NPM_PACKAGE_NAME_TEMPLATE);
    }

    public function handle()
    {
        $server = app($this->argument('class'));

        $versionStorage = new ServerVersionStorage(storage_path(), 'verdaccio');
        $versionStorage->increaseLockVersionWhenDifferentSignatures(
            $server,
            0,
            $this->isMinorOption() ? 1 : 0,
            $this->isMinorOption() ? 0 : 1
        );

        try {
            $generator = new JsGenerator(
                $server,
                $versionStorage,
                $this->getRegistryUrl(),
                $this->getProjectArgument(),
                $this->getScopeArgument(),
                $this->getOnlyOption()
            );

            $generator->go(storage_path('verdaccio'));
            $this->info("Generated");
        } finally {
            $versionStorage->writeLockFile();
        }
    }

    protected function makeClassname(string $entity)
    {
        return 'Rpc' . ucfirst($this->getProjectArgument()) . ucfirst($this->getScopeArgument()) . ucfirst($entity);
    }

    protected function prepareOutputFolder(): string
    {
        $filesystem = new Filesystem();
        $path = storage_path("npm/" . ltrim($this->getOutputDirArgument(), DIRECTORY_SEPARATOR));

        if (!$filesystem->isDirectory($path)) {
            $filesystem->makeDirectory($path, 0777, true);
        }

        return $path;
    }
}
