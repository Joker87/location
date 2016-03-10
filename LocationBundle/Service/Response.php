<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 10.03.16
 * Time: 22:07
 */

namespace LocationBundle\Service;


class Response implements \IteratorAggregate
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
     * @var array
     */
    private $locations = [];

    /**
     * Response constructor.
     * @param string $response
     */
    public function __construct($response)
    {
        $this->parse((array) json_decode($response, true));
    }

    /**
     * @param array $data
     */
    private function parse(array $data)
    {
        if (!isset($data['success'], $data['data'])) {
            throw new \LogicException('Invalid response format');
        }
        $this->success = $data['success'];

        if ($this->isSuccess()) {
            $this->locations = $data['data']['locations'];
        } else {
            $this->errorMessage = $data['data']['message'];
            $this->errorCode = $data['data']['code'];
        }
    }

    /**
     * @return LocationIterator
     */
    public function getIterator()
    {
        return new LocationIterator($this);
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

    /**
     * @param $index
     * @return Location
     */
    public function getLocation($index)
    {
        return $this->hasLocation($index) ? new Location($this->locations[$index]) : null;
    }

    /**
     * @param int $index
     * @return bool
     */
    public function hasLocation($index)
    {
        return isset($this->locations[$index]);
    }
}