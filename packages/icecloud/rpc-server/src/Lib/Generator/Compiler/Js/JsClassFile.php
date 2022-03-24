<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js;


use IceCloud\RpcServer\Lib\Contracts\Generator\CompileClassFile;
use IceCloud\RpcServer\Lib\Generator\Generator;
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsFile;

class JsClassFile extends JsFile implements CompileClassFile
{
    const CLASSNAME_FORMAT = 'Rpc%s%s%s';

    private string $relativeFolder;
    private string $className;
    private array $namespace;

    /**
     * @var JsClassFile[]
     */
    protected array $uses = [];

    /**
     * JsClassTarget constructor.
     * @param JsGenerator $generator Генератор
     * @param array $namespace Пространство имен - массив имен, которые будут преобразованы в PascalCase
     * @param $entityName
     */
    public function __construct(JsGenerator $generator, array $namespace, $entityName)
    {
        $this->namespace = array_map(function ($value) {
            return ucfirst($value);
        }, $namespace);

        $this->relativeFolder = implode(DIRECTORY_SEPARATOR, array_merge(['src'], $this->namespace));

        $this->className = $this->makeClassname($generator, $entityName);

        parent::__construct(
            $generator,
            $this->relativeFolder .
            DIRECTORY_SEPARATOR .
            $this->className . '.mjs'
        );

    }

    /**
     * Получить относительную папку назначения
     * @return string
     */
    public function getRelativeFolder(): string
    {
        return $this->relativeFolder;
    }

    /**
     * Использовать иной класс в импорте
     * @param JsClassFile $class
     * @return $this
     */
    public function use(JsClassFile $class): self
    {
        $this->uses[$class->getClassName()] = $class;
        return $this;
    }

    /**
     * Класс
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return JsClassFile[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * Создать имя класса
     * @param Generator $generator
     * @param string $entityName
     * @return string
     */
    protected function makeClassName(Generator $generator, string $entityName): string
    {
        return sprintf(
            self::CLASSNAME_FORMAT,
            ucfirst($generator->getProjectSlug()),
            ($generator->getScopeSlug() !== null ? ucfirst($generator->getScopeSlug()) : ''),
            ucfirst($entityName)
        );
    }

    public function getNamespace(): array
    {
        return $this->namespace;
    }
}
