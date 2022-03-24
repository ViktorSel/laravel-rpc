<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js;


use IceCloud\RpcServer\Lib\Contracts\Generator\CompileFile;
use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsImportedFile;
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\Compiler\File;
use IceCloud\RpcServer\Lib\Generator\View\ScriptRender;
use Illuminate\Support\Str;
use function view;

class JsFile extends File implements JsImportedFile, CompileFile
{
    public function __construct(JsGenerator $generator, string $filename)
    {
        parent::__construct(
            $generator,
            $generator->getPackageName() . DIRECTORY_SEPARATOR . ltrim($filename, DIRECTORY_SEPARATOR)
        );
    }

    public function compile(string $outputFolder)
    {
        $this->writeContent(
            $outputFolder,
            view($this->viewName, $this->viewArguments)->render()
        );
    }

    public function import(File $file): string
    {
        $leftFilename = $this->getFilename();
        $rightFilename = $file->getFilename();

        $removes = implode(
            DIRECTORY_SEPARATOR, array_intersect_assoc(
                explode(DIRECTORY_SEPARATOR, $rightFilename),
                explode(DIRECTORY_SEPARATOR, $leftFilename)
            ));

        $right = explode(
            DIRECTORY_SEPARATOR,
            ltrim(Str::after($rightFilename, $removes), DIRECTORY_SEPARATOR)
        );
        $left = explode(
            DIRECTORY_SEPARATOR,
            ltrim(Str::after($leftFilename, $removes), DIRECTORY_SEPARATOR)
        );

        // Чистим конечный у подключающего
        array_pop($left);

        $path = array_fill(0, count($left), '..');

        if (count($path) <= 0) {
            $path[] = '.';
        }

        return implode( DIRECTORY_SEPARATOR, array_merge($path, $right) );
    }

}
