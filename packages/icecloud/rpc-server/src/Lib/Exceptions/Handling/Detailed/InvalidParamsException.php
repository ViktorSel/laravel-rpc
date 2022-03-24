<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Handling\Detailed;


use IceCloud\RpcServer\Lib\Response;
use IceCloud\RpcServer\Lib\Exceptions\Handling\HandlingException;

class InvalidParamsException extends HandlingException
{
    public function __construct(string $message, ?array $data = null)
    {
        parent::__construct(Response::INVALID_PARAMS_CODE, $message, $data);
    }

    static function prefixedFields(InvalidParamsException $exception, string $prefix = null): self
    {
        if ($prefix === null || $exception->getData() === null) {
            return $exception;
        }
        $forwarded = [];
        foreach ($exception->getData() as $field => $errors) {
            $forwarded[$prefix . '.' . $field] = $errors;
        }

        return new self($exception->getMessage(), $forwarded);
    }

    static function combine(InvalidParamsException $exception, InvalidParamsException $additional): self
    {
        $data = array_merge($exception->getData() ?? [], $additional->getData() ?? []);

        return new self(
            $additional->getMessage(),
            empty($data) ? null : $data
        );
    }
}
