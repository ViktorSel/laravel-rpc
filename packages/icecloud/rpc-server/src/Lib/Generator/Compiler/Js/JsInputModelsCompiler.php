<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js;


use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsCustomClassVariable;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsModelRelation;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsProcedure;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsVariableWithDefinition;
use IceCloud\RpcServer\Lib\Generator\Generator;
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\Walkers\ArgumentsWalker;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Server;

class JsInputModelsCompiler
{
    const COMMON_FORMS_NAMESPACE = ['Forms', 'Common'];
    const INPUT_FORMS_NAMESPACE = ['Forms', 'Inputs'];

    private $view;
    private $arguments;
    private $generator;

    public function __construct(JsGenerator $generator, string $view, array $arguments = [])
    {
        $this->view = $view;
        $this->arguments = $arguments;
        $this->generator = $generator;
    }

    /**
     * @param Server $server
     * @param string $outputFolder
     * @param callable|null $on
     * @return JsInputModelsCompilingResult
     */
    public function compile(Server $server, string $outputFolder, ?callable $on = null) : JsInputModelsCompilingResult
    {
        $recurrences = [];
        $occurrences = [];

        $files = [];

        // Обходим и проверяем совпадения объектных структур, чтобы не плодить модели
        // Просто сверяем хеши
        foreach ($this->generator->getProcedures() as $procedure) {
            $walker = new ArgumentsWalker($procedure);

            // Собираем хеши
            $walker->run(function (ValueDefinition $definition, array $pool) use(&$recurrences, &$occurrences, &$files) {
                // Говорим, что нас интересуют только объекты
                if (!$definition instanceof ObjectDefinition) {
                    return;
                }

                // Счетчик вхождений
                if (!array_key_exists($definition->hash(), $occurrences)) {
                    $occurrences[$definition->hash()] = 1;
                } else {
                    $occurrences[$definition->hash()]++;
                }

                // Модель не используется повторно
                if ($occurrences[$definition->hash()] < 2) {
                    return;
                }

                $file = new JsInputModelFile(
                    $this->generator,
                    $definition,
                    self::COMMON_FORMS_NAMESPACE,
                    ucfirst($definition->getName())
                );

                $files[] = $recurrences[$definition->hash()] = $file->view($this->view, $this->arguments);
            });
        }

        $jsProcedures = [];

        foreach ($this->generator->getProcedures() as $procedure) {
            $walker = new ArgumentsWalker($procedure);

            $form = null;

            if ($walker->getSchema()->complex()) {
                $form = new JsInputModelFile(
                    $this->generator,
                    $walker->getSchema(),
                    self::INPUT_FORMS_NAMESPACE,
                    $procedure->getNameScope()
                );

                $files[] = $form->view($this->view, $this->arguments);
            }

            // Обход в порядке вложенности всех объектов, для которых требуется создание форм
            $walker->run(function (ValueDefinition $definition, array $pool) use (&$recurrences,  &$files) {
                if (!$definition instanceof ObjectDefinition) {
                    return null;
                }

                $form = array_key_exists($definition->hash(), $recurrences)
                    ? $recurrences[$definition->hash()]
                    : null;

                if (!$form) {
                    $form = new JsInputModelFile(
                        $this->generator,
                        $definition,
                        self::INPUT_FORMS_NAMESPACE,
                        $definition->getPascalName()
                    );

                    $files[] = $form->view($this->view, $this->arguments);
                    $recurrences[$definition->hash()] = $form;
                }

                $last = array_pop($pool); /* @var $last JsInputModelFile */

                if ($last === null) {
                    return $form;
                }

                $arrayable = $definition->getParent() instanceof ArrayDefinition;
                $propName = $arrayable ? $definition->getParent()->getName() : $definition->getName();

                $last->use($form);

                $last->applyRelation(
                    new JsModelRelation(
                        $last->getProperty($propName),
                        $form,
                        $arrayable
                    )
                );

                return $form;

            }, $form === null ? [] : [$form]);

            $jsProcedure = new JsProcedure($procedure);

            if ($form !== null) {
                $jsProcedure->setDedicatedArgument(new JsCustomClassVariable('form', $form));
            } else {
                foreach ($walker->getSchema()->getProperties() as $property) {
                    $argument = new JsVariableWithDefinition($property);
                    $jsProcedure->addArgument($argument);

                    if (array_key_exists($property->hash(), $recurrences)) {
                        $argument->setUse($recurrences[$property->hash()]);
                    }
                }
            }

            $jsProcedures [$procedure->getFullQualifiedNameWithoutService()] = $jsProcedure;
        }

        return new JsInputModelsCompilingResult($files, $jsProcedures);
    }
}
