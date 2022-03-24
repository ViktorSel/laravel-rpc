<?php


namespace IceCloud\RpcServer\Lib\Middlewares;


use IceCloud\RpcServer\Lib\Exceptions\Handling\BreakException;
use IceCloud\RpcServer\Lib\Middleware;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;
use IceCloud\RpcServer\Lib\Utils\MethodsMask;
use Symfony\Component\HttpFoundation\IpUtils;

abstract class IpGuard extends Middleware
{
    /**
     * @var bool
     */
    protected bool $enableWhiteList = false;
    /**
     * @var bool
     */
    protected bool $enableBlackList = false;

    /**
     * Белый список IP
     *
     * Формат: [Маска метода => [IP, ...], ...]
     * Пример: ['Service.V1.*' = ['127.0.0.1', '::1']]
     *
     * @return array
     */
    abstract protected function whiteList(): array;
    /**
     * Черный список IP
     *
     * Формат: [Маска метода => [IP, ...], ...]
     * Пример: ['Service.V1.*' = ['127.0.0.1', '::1']]
     *
     * @return array
     */
    abstract protected function blackList(): array;

    public function handle(Request $request, \Closure $closure)
    {
        $clientIp = $request->getHttpRequest()->ip();

        if ($this->enableBlackList) {
            foreach ($this->blackList() as $methodMask=>$allowedIps) {
                $allowedIps = array_map(function ($ip) {
                    return trim($ip);
                }, $allowedIps);

                if (!MethodsMask::fastMatch($request->getMethod(), $methodMask)) {
                    continue;
                }

                if (IpUtils::checkIp($clientIp, $allowedIps)) {
                    throw new BreakException();
                }
            }
        }

        if ($this->enableWhiteList) {
            foreach ($this->whiteList() as $methodMask=>$allowedIps) {
                if (!MethodsMask::fastMatch($request->getMethod(), $methodMask)) {
                    continue;
                }

                $allowedIps = array_map(function ($ip) {
                    return trim($ip);
                }, $allowedIps);

                if (!IpUtils::checkIp($clientIp, $allowedIps)) {
                    throw new BreakException();
                }
            }
        }

        return $closure($request);
    }

    public function argumentsSchema(ArgumentsSchema $arguments)
    {
        // void
    }

    public function resultSchema(ResultSchema $result)
    {
        // void
    }
}
