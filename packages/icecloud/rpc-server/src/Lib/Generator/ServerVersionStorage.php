<?php


namespace IceCloud\RpcServer\Lib\Generator;


use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Schema\Workers\SchemaAccumulator;
use IceCloud\RpcServer\Lib\Server;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ServerVersion
 *
 * Пишет версию в storage системы на основе сигнатуры Rpc сервера. Нужен для контроля версий
 *
 * @package IceCloud\RpcServer\Lib\Generator
 * @author a.kazakov <a.kazakov@iceberg.ru>
 */
class ServerVersionStorage
{
    const SIGNATURE_FILENAME = 'rpc.server.lock';
    const DEFAULT_VERSION = '1.0.0';

    private string $version = self::DEFAULT_VERSION;
    private ?string $signature = null;

    private Filesystem $fs;
    private string $filename;

    /**
     * ServerVersion constructor.
     * @param string $storingFolder Папка где будет хранится lock файл версии сервера
     * @param string $name Имя хранилища
     */
    public function __construct(string $storingFolder, string $name)
    {
        $this->filename = rtrim($storingFolder, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            sprintf('rpc.%s.lock.json', $name);

        $this->fs = new Filesystem();
        $this->readLockFile();
    }

    /**
     * Получить имя lock файла
     * @return string
     */
    public function getLockFilename()
    {
        return $this->filename;
    }

    /**
     * Прочитать lock файл
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function readLockFile(): void
    {
        $filename = $this->getLockFilename();

        if (!$this->fs->isFile($filename)) {
            return;
        }

        $data = json_decode(
            $this->fs->get($filename), true
        );

        $this->version = $data['version'];
        $this->signature = $data['signature'];
    }

    /**
     * Записать версию в lock файл
     */
    public function writeLockFile(): void
    {
        $filename = $this->getLockFilename();
        $this->fs->put($filename, json_encode([
            'version' => $this->version,
            'signature' => $this->signature
        ]));
    }

    /**
     * Получить текущую версию
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Увеличить версию на указанные каунтеры
     * @param Server $server
     * @param int $majorIncrease
     * @param int $minorIncrease
     * @param int $buildIncrease
     * @throws BindingResolutionException
     */
    public function increaseLockVersionWhenDifferentSignatures(Server $server, int $majorIncrease = 0, int $minorIncrease = 0, int $buildIncrease = 0): void
    {
        $signature = $this->bakeServerSignature($server->getProcedures());

        if ($this->signature === $signature) {
            return;
        }

        $this->signature = $signature;

        $version = explode('.', $this->version);
        $version[0] += $majorIncrease;
        $version[1] += $minorIncrease;
        $version[2] += $buildIncrease;

        $this->version = implode('.', $version);
    }

    /**
     * Запечь сигнатуру сервера на основе имен процедур, описания их аргументов и результатов
     * @param Procedure[] $procedures
     * @throws BindingResolutionException
     */
    protected function bakeServerSignature(array $procedures)
    {
        $data = [];
        foreach ($procedures as $procedure) {
            $accumulator = new SchemaAccumulator();

            $accumulator->pushAll($procedure->createMiddlewares());
            $accumulator->push($procedure);
            $arguments = $accumulator->buildProcedureArgumentsSchema();
            $result = $accumulator->buildProcedureResultSchema();

            $argumentsHash = md5(serialize($arguments));
            $resultHash = md5(serialize($result));

            $data[] = md5($procedure->getFullQualifiedName() . $argumentsHash . $resultHash);
        }
        return md5(implode('', $data));
    }
}
