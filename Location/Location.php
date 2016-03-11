<?php
namespace LocationBundle\Location;
use LocationBundle\Exception\InvalidLocationException;

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
     * @param string $name
     * @param Coordinates $coordinates
     */
    public function __construct($name, Coordinates $coordinates)
    {
        $this->name = $name;
        $this->coordinates = $coordinates;
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

    /**
     * @param array $locationData
     * @return Location
     * @throws InvalidLocationException
     */
    public static function create(array $locationData)
    {
        if (!isset($locationData['name'], $locationData['coordinates'])) {
            throw new InvalidLocationException('Invalid location format');
        }

        return new self($locationData['name'], Coordinates::create($locationData['coordinates']));
    }
}