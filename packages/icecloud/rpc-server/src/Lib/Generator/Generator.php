<?php


namespace IceCloud\RpcServer\Lib\Generator;


use IceCloud\RpcServer\Lib\Generator\Items\JsClassTarget;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Server;
use IceCloud\RpcServer\Lib\Utils\MethodsMask;
use Illuminate\Filesystem\Filesystem;

abstract class Generator
{
    /**
     * @var Server
     */
    protected Server $server;
    private string $projectSlug;
    private ?string $scopeSlug;
    private ?string $description = null;
    protected Filesystem $fs;

    protected array $procedures = [];


    protected ServerVersionStorage $versionStorage;

    public function __construct(Server $server, ServerVersionStorage $versionStorage, string $projectSlug, ?string  $scopeSlug = null, ?array $onlyFilter = null)
    {
        $this->versionStorage = $versionStorage;
        $this->server = $server;
        $this->projectSlug = $projectSlug;
        $this->scopeSlug = $scopeSlug;
        $this->fs = new Filesystem();

        if ($onlyFilter === null) {
            $this->procedures = $server->getProcedures();
            return;
        }

        $this->procedures = array_filter($server->getProcedures(), function (Procedure $procedure) use($onlyFilter) {
            foreach ($onlyFilter as $mask) {
                if (MethodsMask::fastMatch($procedure->name(), $mask)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * @return array|Procedure[]
     */
    public function getProcedures(): array
    {
        return $this->procedures;
    }

    /**
     * @return Filesystem
     */
    public function getFs(): Filesystem
    {
        return $this->fs;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Описание проекта
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description === null ? $this->generateDescription() : $this->description;
    }

    /**
     * Сгенерировать описание клиента
     * @return string
     */
    private function generateDescription(): string {
        return sprintf(
            "The RPC client for the %s %s",
            ucfirst($this->projectSlug),
            $this->scopeSlug
                ? sprintf("and the %s scope", ucfirst($this->scopeSlug))
                : ''
        );
    }

    /**
     * Установить кастомное описание проекта
     * @param string|null $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Слаг проекта
     * @return string
     */
    public function getProjectSlug(): string
    {
        return $this->projectSlug;
    }

    /**
     * Слаг скоупа
     * @return string|null
     */
    public function getScopeSlug(): ?string
    {
        return $this->scopeSlug;
    }

    abstract protected function compile(string $outputFolder);
    abstract protected function makeOutputFolderName(string $outputFolder);

    /**
     * Запустить генератор
     * @param string $outputFolder
     * @throws \Throwable
     */
    public function go(string $outputFolder): void
    {
        $folderName = $this->makeOutputFolderName($outputFolder);

        if ($this->fs->isDirectory($outputFolder)) {
            $this->fs->cleanDirectory($folderName);
        } else {
            $this->fs->makeDirectory($folderName, 0777, true);
        }

        try {
            $this->compile($outputFolder);
            $this->versionStorage->writeLockFile();
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @return ServerVersionStorage
     */
    public function getVersionStorage(): ServerVersionStorage
    {
        return $this->versionStorage;
    }
}
