<?php
namespace LocationBundle\Location;
use LocationBundle\Exception\InvalidCoordinatesException;

/**
 * Class Coordinates
 * @package LocationBundle\Service
 */
class Coordinates
{
    /**
     * @var float
     */
    private $lat;
    /**
     * @var float
     */
    private $lng;

    /**
     * Coordinates constructor.
     * @param float $lat
     * @param float $lng
     */
    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param array $data
     * @return Coordinates
     * @throws InvalidCoordinatesException
     */
    public static function create(array $data)
    {
        if (!isset($data['lat'], $data['long'])) {
            throw new InvalidCoordinatesException('Invalid location format');
        }
        return new self($data['lat'], $data['long']);
    }
}