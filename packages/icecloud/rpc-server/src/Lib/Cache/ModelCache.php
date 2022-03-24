<?php


namespace IceCloud\RpcServer\Lib\Cache;


use Brick\VarExporter\VarExporter;
use IceCloud\RpcServer\Lib\Contracts\Cache\ExportedClassContract;
use IceCloud\RpcServer\Lib\Procedure;
use Illuminate\Filesystem\Filesystem;

/**
 * Специализированный кеш для моделей - инстансов {@link ExportedClassContract}.
 * Экспортирует инстанс в php файл, если файл процедуры был обновлен.
 *
 * Правило инвалидации кеша : если время записи кеша раньше чем время записи файла процедуры
 *
 * @package IceCloud\RpcServer\Lib\Cache
 * @author a.kazakov
 */
class ModelCache
{
    const PERMISSIONS = 0755;

    private Filesystem $filesystem;

    private string $pivotFilename;
    private string $cacheFilename;

    private Procedure $procedure;

    /**
     * ModelCache constructor.
     *
     * @param string $cachePath Путь до папки для кеширования
     * @param Procedure $procedure Инстанс процедуры
     */
    public function __construct(string $cachePath, Procedure $procedure)
    {
        $this->procedure = $procedure;
        $this->filesystem = new Filesystem();
        $this->pivotFilename = (new \ReflectionClass($procedure))->getFileName();
        $this->cacheFilename =
            rtrim($cachePath, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            $procedure->name() .
            '.php';
    }

    /**
     * Создать содержимое php файла
     * @param ExportedClassContract $data
     * @return string
     */
    protected function makeContent(ExportedClassContract $data): string
    {
        return "<?php return " . VarExporter::export($data, VarExporter::CLOSURE_SNAPSHOT_USES) . ";";
    }

    /**
     * Записать в кеш инстанс
     * @param ExportedClassContract $data
     */
    protected function put(ExportedClassContract $data)
    {
        $cacheDirectory = dirname($this->cacheFilename);

        if (!$this->filesystem->isDirectory($cacheDirectory)) {
            $this->filesystem->makeDirectory(
                $cacheDirectory,
                self::PERMISSIONS,
                true,
                true
            );
        }

        $this->filesystem->put($this->cacheFilename, $this->makeContent($data));
    }

    /**
     * Устарел ли кеш (инвалидация)
     * @return bool
     */
    public function isOutdated(): bool
    {
        $sourceTime = filemtime($this->pivotFilename);
        $cachedTime = $this->filesystem->isFile($this->cacheFilename)
            ? filemtime($this->cacheFilename)
            : 0;

        return $cachedTime < $sourceTime;
    }

    /**
     * Запомнить инстанс в кеше
     * @param callable $callback Функция, которая должна вернуть экземпляр {@link ExportedClassContract}
     * @param bool $force Принудительное обновление кеша
     * @return ExportedClassContract
     */
    public function remember(callable $callback, bool $force = false): ExportedClassContract
    {
        if (!$force && !$this->isOutdated()) {
            return $this->procedure->includeModelCacheWithSelfScope($this);
        }

        $this->put($data = $callback());
        return $data;
    }


    /**
     * @return string
     */
    public function getCacheFilename(): string
    {
        return $this->cacheFilename;
    }

}
