<?php


namespace Tests\Unit;


use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\TestDefaultValueException;
use IceCloud\RpcServer\Lib\Models\CompiledValidationInstructions;
use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;
use IceCloud\RpcServer\Lib\Schema\Workers\DefaultValuesFiller;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;

class ArgumentsSchemaTest extends \Tests\TestCase
{
    public function test_compileSimpleDefaults()
    {
        $schema = new ArgumentsSchema();
        $schema->int("test")->default(1);

        $validationData = $schema->compileValidationInstructions();

        $this->assertTrue(
            count(array_diff(
                $validationData->getDefaults(),
                ["test" => 1]
            )) === 0
        );
    }

    public function test_compileDefaultsOnNestedObjects() {
        $schema = new ArgumentsSchema();
        $schema->object("test", function (ObjectBlueprint $blueprint) {
            $blueprint->array("array", function () {
                return new ObjectDefinition(function (ObjectBlueprint $blueprint) {
                    $blueprint->int("some_property")->default(1);
                });
            });

            $blueprint->string("title")->default("foo");
        });

        $validationData = $schema->compileValidationInstructions();

        $this->assertTrue(
            count(array_diff(
                $validationData->getDefaults(),
                [
                    "test.array.*.some_property" => 1,
                    "test.title" => "foo"
                ]
            )) === 0
        );
    }

    public function test_defaultValuesFiller() {
        $data = [
            "key_1" => 0,
            "object_1" => [
                "key_2" => 1,
            ],
            "array_1" => [
                [
                    "key_3" => 1
                ],
                [
                    "object_2" => [
                        "key_4" => 2,
                        "nested_array" => [
                            [],
                            []
                        ]
                    ]
                ]
            ]
        ];

        $instructions = new CompiledValidationInstructions([], [], [
            "default" => 1,
            "object_1.default" => 1,
            "array_1.*.default" => 1,
            "array_1.*.object_2.default" => 1,
            "array_1.*.object_2.nested_array.*.field" => 1,
        ]);

        $filler = new DefaultValuesFiller($instructions);
        $filler->fill($data);

        $assert = [
            "default" => 1,
            "key_1" => 0,
            "object_1" => [
                "key_2" => 1,
                "default" => 1,
                "nested_array" => [
                    [
                        "default" => 0
                    ],
                    [
                        "default" => 0
                    ]
                ]
            ],
            "array_1" => [
                [
                    "key_3" => 1,
                    "default" => 1
                ],
                [
                    "default" => 1,
                    "object_2" => [
                        "key_4" => 2,
                        "default" => 1,
                        "nested_array" => [
                            [
                                "field" => 1
                            ],
                            [
                                "field" => 1
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->assertTrue(
            $this->hasIdenticalArrays($data, $assert)
        );
    }

    public function test_validationConsistent() {

        $def = new StringDefinition();
        $def
            ->required()
            ->nullable();

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof ConflictConstraintsException);
        }

        $def = new StringDefinition();
        $def
            ->required()
            ->default("Test");

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof ConflictConstraintsException);
        }

        $def = new StringDefinition();
        $def
            ->uuidFormat()
            ->min(2)
            ->default("1");

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof TestDefaultValueException);
        }


    }

    public function test_stringDefinition() {
        $def = new StringDefinition();
        $def
            ->required()
            ->nullable();

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof ConflictConstraintsException);
        }

        $def = new StringDefinition();
        $def
            ->required()
            ->default("Test");

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof ConflictConstraintsException);
        }

        $def = new StringDefinition();
        $def
            ->uuidFormat()
            ->min(2)
            ->default("1");

        try {
            $def->getConstraints()->prepare();
        } catch (\Throwable $exception) {
            $this->assertTrue( $exception instanceof TestDefaultValueException);
        }

        $def = new StringDefinition();
    }

    private function hasIdenticalArrays($arr1, $arr2) : bool
    {
        return count($this->arrayDiffRecursive($arr1, $arr2)) === 0;
    }

    private function arrayDiffRecursive($arr1, $arr2) : array
    {
        $outputDiff = [];

        foreach ($arr1 as $key => $value)
        {
            if (array_key_exists($key, $arr2))
            {
                if (is_array($value))
                {
                    $recursiveDiff = $this->arrayDiffRecursive($value, $arr2[$key]);

                    if (count($recursiveDiff))
                    {
                        $outputDiff[$key] = $recursiveDiff;
                    }
                }
                else if (!in_array($value, $arr2))
                {
                    $outputDiff[$key] = $value;
                }
            }
            else if (!in_array($value, $arr2))
            {
                $outputDiff[$key] = $value;
            }
        }

        return $outputDiff;
    }
}
