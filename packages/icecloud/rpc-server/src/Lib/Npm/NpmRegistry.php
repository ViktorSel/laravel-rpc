<?php


namespace IceCloud\RpcServer\Lib\Npm;


use GuzzleHttp\Client;
use IceCloud\RpcServer\Lib\Npm\Models\PackageMeta;
use Psr\Http\Message\ResponseInterface;

class NpmRegistry
{
    private $registryHost;

    public function __construct(string $registryHost)
    {
        $this->registryHost = $registryHost;
    }

    public function packageMeta(string $package) : PackageMeta {
        return new PackageMeta(
            $this->call($package)
        );
    }

    protected function call($uri) : array {
        $data = (new Client())->get("{$this->registryHost}/{$uri}");
        return json_decode($data->getBody()->getContents(), true);
    }

    /**
     * @return string
     */
    public function getRegistryHost(): string
    {
        return $this->registryHost;
    }
}
