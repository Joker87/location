<?php
namespace LocationBundle\Service;

/**
 * Class Location
 * @package LocationBundle\Service
 */
class Location
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Coordinates
     */
    private $coordinates;

    /**
     * Location constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['name'], $data['coordinates']['lat'], $data['coordinates']['long'])) {
            throw new \LogicException('Invalid location format');
        }
        $this->name = $data['name'];
        $this->coordinates = new Coordinates($data['coordinates']['lat'], $data['coordinates']['long']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
}