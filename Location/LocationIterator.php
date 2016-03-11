<?php
namespace LocationBundle\Location;

/**
 * Class LocationIterator
 * @package LocationBundle\Service
 */
class LocationIterator implements \Iterator
{
    /**
     * @var int
     */
    private $position = 0;
    /**
     * @var Response
     */
    private $response;

    /**
     * LocationIterator constructor.
     * @param Response $response
     */
    public function __construct(LocationCollection $response)
    {
        $this->response = $response;
    }

    /**
     * @return Location
     */
    public function current()
    {
        return $this->response->getLocation($this->position);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->response->hasLocation($this->position);
    }
}