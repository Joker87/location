<?php
namespace LocationBundle\Service;

use LocationBundle\Exception\RequestFailedException;
use Guzzle\Http\Client as CurlClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Client
 * @package LocationBundle\Service
 */
class Client
{
    /**
     * @var CurlClient
     */
    private $curlClient;

    /**
     * ServiceClient constructor.
     * @param CurlClient $curlClient
     */
    public function __construct(CurlClient $curlClient)
    {
        $this->curlClient = $curlClient;
    }

    /**
     * @return \LocationBundle\Location\LocationCollection
     * @throws RequestFailedException
     * @throws \Exception
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getLocations()
    {
        $curlResponse = $this->curlClient->get(DIRECTORY_SEPARATOR . 'locations')->send();
        if ($curlResponse->getStatusCode() >= 400) {
            throw new HttpException('HTTP error occurs: ' . $curlResponse->getBody(true), $curlResponse->getStatusCode());
        }

        $response = new Response($curlResponse->json());
        
        if (!$response->isSuccess()) {
            throw new RequestFailedException($response->getErrorMessage(), $response->getErrorCode());
        }

        return $response->getLocations();
    }
}