<?php
namespace LocationBundle\Service;

use LocationBundle\Component\CurlClient;

/**
 * Class ServiceClient
 * @package LocationBundle\Service
 */
class ServiceClient
{
    /**
     * @var CurlClient
     */
    private $curlClient;

    /**
     * @var string
     */
    private $host;

    /**
     * ServiceClient constructor.
     * @param CurlClient $curlClient
     * @param string $host
     */
    public function __construct(CurlClient $curlClient, $host)
    {
        $this->curlClient = $curlClient;
        $this->host = $host;
    }

    /**
     * @return Response
     * @throws LocationServiceException
     * @throws \Exception
     */
    public function getLocations()
    {
        $this->curlClient->setUrl($this->host . DIRECTORY_SEPARATOR . 'locations');
        $response = new Response($this->curlClient->exec());
        
        if (!$response->isSuccess()) {
            throw new LocationServiceException($response->getErrorMessage(), $response->getErrorCode());
        }

        return $response;
    }
}