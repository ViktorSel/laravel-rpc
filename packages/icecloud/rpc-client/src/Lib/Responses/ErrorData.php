<?php


namespace IceCloud\RpcClient\Lib\Responses;


use IceCloud\RpcClient\Lib\Exceptions\Detailed\InvalidResponseException;
use Illuminate\Support\Facades\Validator;

/**
 * Class ErrorData
 * @package IceCloud\RpcClient\Lib\Responses
 * @author a.kazakov <a.kazakov@iceberg.ru>
 *
 * @todo Добавить util методы для извлечения ошибок ДДО
 */
class ErrorData
{
    const ATTR_CODE = 'code';
    const ATTR_MESSAGE = 'message';
    const ATTR_DATA = 'data';

    private int $code;
    private ?array $data;
    private ?string $message;

    public function __construct(array $data)
    {
//        $validator = Validator::make($data, [
//            self::ATTR_CODE => ['required', 'integer'],
//            self::ATTR_MESSAGE => ['nullable', 'string'],
//            self::ATTR_DATA => ['nullable', 'array']
//        ]);
//
//        if ($validator->fails()) {
//            throw new InvalidResponseException(
//                "Блок ошибки не прошел проверку. Встречена ошибка : " . $validator->getMessageBag()->first()
//            );
//        }
//
        $this->code = $data[self::ATTR_CODE];
        $this->message = $data[self::ATTR_MESSAGE] ?? null;
        $this->data = $data[self::ATTR_DATA] ?? null;
    }

    /**
     * @return int|mixed
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return mixed|string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array|mixed|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
