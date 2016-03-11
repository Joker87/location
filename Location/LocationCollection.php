<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 11.03.16
 * Time: 21:02
 */

namespace LocationBundle\Location;


class LocationCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $locations = [];

    /**
     * LocationCollection constructor.
     * @param array $locations
     */
    public function __construct(array $locations)
    {
        $this->locations = $locations;
    }
    
    /**
     * @return LocationIterator
     */
    public function getIterator()
    {
        return new LocationIterator($this);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->locations);
    }

    /**
     * @param $index
     * @return Location
     */
    public function getLocation($index)
    {
        return $this->hasLocation($index) ? Location::create($this->locations[$index]) : null;
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