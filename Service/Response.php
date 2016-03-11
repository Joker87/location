<?php
namespace LocationBundle\Service;


use LocationBundle\Location\LocationCollection;

/**
 * Class Response
 * @package LocationBundle\Service
 */
class Response
{
    /**
     * @var bool
     */
    private $success = false;
    /**
     * @var string
     */
    private $errorMessage;
    /**
     * @var string
     */
    private $errorCode;
    /**
     * @var LocationCollection
     */
    private $locations;

    /**
     * Response constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['success'], $data['data'])) {
            throw new \LogicException('Invalid response format');
        }
        $this->success = (bool) $data['success'];

        if ($this->success) {
            $this->locations = new LocationCollection($data['data']['locations']);
        } else {
            $this->errorMessage = $data['data']['message'];
            $this->errorCode = $data['data']['code'];
        }
    }

    /**
     * @return LocationCollection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}