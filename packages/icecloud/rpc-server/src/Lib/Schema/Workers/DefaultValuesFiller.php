<?php


namespace IceCloud\RpcServer\Lib\Schema\Workers;


use IceCloud\RpcServer\Lib\Models\CompiledValidationInstructions;

/**
 * Реализует заполнение значениями по умолчанию
 *
 * @package IceCloud\RpcServer\Lib\Schema\Workers
 */
class DefaultValuesFiller
{
    private CompiledValidationInstructions $instructions;

    public function __construct(CompiledValidationInstructions $instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * Рекурсивное применение значений. Рекурсия нужна только для массивов.
     * @param array $data
     * @param array $path
     * @param $value
     */
    private function enter(array &$data, array $path, $value)
    {
        $last = &$data;

        while (($item = array_shift($path)) !== null) {
            $isLast = count($path) <= 0;

            if ($item === '*' && is_array($last)) {
                foreach ($last as $index=>&$items) {
                    $this->enter($items, $path, $value);
                }
                break;
            }

            // Проверяем свойство только по последнему элементу в пути, чтобы не залезть куда не надо
            if ($isLast && !isset($last[$item])) {
                $last[$item] = $value;
                break;
            }

            // Проверяем
            if (!array_key_exists($item, $last)) {
                break;
            }

            $last = &$last[$item];
        }
    }

    /**
     * Заполнить значениями по умолчанию
     * @param array $data
     */
    public function fill(array &$data)
    {
        foreach ($this->instructions->getDefaults() as $path => $value) {
            $preparedPath = explode('.', $path);
            $this->enter($data, $preparedPath, $value);
        }
    }
}
