<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler;


use IceCloud\RpcServer\Lib\Contracts\Generator\CompileFile;
use IceCloud\RpcServer\Lib\Generator\Generator;
use Illuminate\Filesystem\Filesystem;

/**
 * Class File
 *
 * Абстракция компилируемого файла
 *
 * @package IceCloud\RpcServer\Lib\Generator\Compiler
 * @author a.kazakov <a.kazakov@iceberg.ru>
 */
abstract class File implements CompileFile
{
    const FILE_ARGUMENT = 'file';
    const GENERATOR_ARGUMENT = 'generator';

    private string $filename;
    private Generator $generator;

    protected ?string $viewName = null;
    protected array $viewArguments = [];

    /**
     * @return array
     */
    public function getViewArguments(): array
    {
        return $this->viewArguments;
    }

    /**
     * @return string|null
     */
    public function getViewName(): ?string
    {
        return $this->viewName;
    }

    /**
     * CompiledFile constructor.
     * @param Generator $generator
     * @param string $filename
     */
    public function __construct(Generator $generator, string $filename)
    {
        $this->generator = $generator;
        $this->filename = $filename;
    }

    /**
     * Относительно имя назначения
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Указать какое представление рендерит файл
     * @param string $view
     * @param array $arguments
     * @return $this
     */
    public function view(string $view, array $arguments = []): self
    {
        $this->viewArguments = array_merge($arguments, [
            self::FILE_ARGUMENT => $this,
            self::GENERATOR_ARGUMENT => $this->getGenerator()
        ]);
        $this->viewName = $view;
        return $this;
    }

    /**
     * Установить ID таргета, чтобы его быстро искать
     * @param string $id
     * @deprecated
     * @return $this
     */
    public function id(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получить ID файла
     * @deprecated
     * @return string|null
     */
    public function getId() : ?string
    {
        return $this->id;
    }

    /**
     * Запись контента
     * @param string $outputFolder
     * @param string $content
     */
    protected function writeContent(string $outputFolder, string $content): void
    {
        $fs = new Filesystem();
        $filename = rtrim($outputFolder, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            $this->getFilename();

        $folder = $fs->dirname($filename);

        if (!$fs->isDirectory($folder)) {
            $fs->makeDirectory($folder, 0777, true);
        }

        $fs->put($filename, $content);
    }
}
