<?php


namespace IceCloud\RpcServer\Lib\Generator;


use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelsCompiler;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsModelRelation;
use IceCloud\RpcServer\Lib\Generator\Walkers\ArgumentsWalker;
use IceCloud\RpcServer\Lib\Npm\NpmRegistry;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Server;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;

class JsGenerator extends Generator
{
    const COMMON_FORMS_NAMESPACE = ['Forms', 'Common'];
    const INPUT_FORMS_NAMESPACE = ['Forms', 'Inputs'];

    const INPUT_FORM_ARGUMENT = 'form';

    const CLIENT_ENTITY = 'client';
    const METHODS_ENTITY = 'methods';

    /**
     * @var JsFile[]
     */
    private array $files = [];

    /**
     * @var NpmRegistry
     */
    private NpmRegistry $npmRegistry;

    public function __construct(Server $server, ServerVersionStorage $versionStorage, string $npmRegistryUrl, string $projectSlug, ?string $scopeSlug = null, ?array $onlyFilter = null)
    {
        parent::__construct($server, $versionStorage, $projectSlug, $scopeSlug, $onlyFilter);
        $this->npmRegistry = new NpmRegistry($npmRegistryUrl);
    }

    /**
     * @return NpmRegistry
     */
    public function getNpmRegistry(): NpmRegistry
    {
        return $this->npmRegistry;
    }

    protected function makeOutputFolderName(string $outputFolder)
    {
        return rtrim($outputFolder, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            $this->getPackageName();
    }

    protected function compile(string $outputFolder, ?callable $on = null) {
        Blade::setEchoFormat('%s');
        Blade::withDoubleEncoding();
        Blade::withoutComponentTags();

        (new JsFile($this, 'package.json'))
            ->view('rpc-server::clients.frontend.package')
            ->compile($outputFolder);

        (new JsFile($this, '.npmrc'))
            ->view('rpc-server::clients.frontend.npmrc')
            ->compile($outputFolder);

        $client = new JsClassFile($this, [], 'Client');
        $methods = new JsClassFile($this, [], 'Methods');

        $inputCompiling = (new JsInputModelsCompiler($this, 'rpc-server::clients.frontend.form.body'))
            ->compile($this->getServer(), $outputFolder);

        foreach ($inputCompiling->getFiles() as $file) {
            $methods->use($file);
            $file->compile($outputFolder);
        }

        $client
            ->view('rpc-server::clients.frontend.client', [
                'methods' => $methods
            ])
            ->use($methods)
            ->compile($outputFolder);

        $preparedJsProcedures = [];

        foreach ($inputCompiling->getJsProcedures() as $name => $jsProc) {
            Arr::set($preparedJsProcedures, $name, $jsProc);
        }

        $methods
            ->view('rpc-server::clients.frontend.methods', [
                'client' => $client,
                'procedures' => $preparedJsProcedures
            ])
            ->use($client)
            ->compile($outputFolder);

        $readme = (new JsFile($this, 'README.md'))
            ->view('rpc-server::clients.frontend.readme.body', [
                'clientFile' => $client,
                'methodsFile' => $methods,
                'inputs' => $inputCompiling
            ]);

        $readme->compile($outputFolder);
    }

    public function getPackageName() {
        return sprintf(
            'ice-lib-rpc-%s%s-client',
            $this->getProjectSlug(),
            $this->getScopeSlug() !== null ? '-' . $this->getScopeSlug() : ''
        );
    }

}
