<?php
namespace LocationBundle\Tests\Service;

use LocationBundle\Location\Location;
use LocationBundle\Service\Response;

/**
 * Class ResponseTest
 * @package LocationBundle\Tests\Service
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseExample()
    {
        $response = new Response([
            'data' => [
                'locations' => [
                    [
                        'name' => 'Eiffel Tower',
                        'coordinates' => [
                            'lat' => 21.12,
                            'long' => 19.56,
                        ]
                    ],
                    [
                        'name' => 'Eiffel Tower',
                        'coordinates' => [
                            'lat' => 21.12,
                            'long' => 19.56,
                        ]
                    ],
                ]
            ],
            'success' => true,
        ]);

        /** @var Location $location */
        foreach ($response->getLocations() as $location) {
            $this->assertInstanceOf('LocationBundle\\Location\\Location', $location);
            $this->assertSame($location->getName(), 'Eiffel Tower');
            $this->assertInstanceOf('LocationBundle\\Location\\Coordinates', $location->getCoordinates());
            $this->assertSame($location->getCoordinates()->getLat(), 21.12);
            $this->assertSame($location->getCoordinates()->getLng(), 19.56);
        }
    }

    public function testResponseError()
    {
        $response = new Response([
            'data' => [
                'message' => 'test error message',
                'code' => 'error code test',
            ],
            'success' => false,
        ]);
        $this->assertNull($response->getLocations());
        $this->assertFalse($response->isSuccess());
        $this->assertSame('error code test', $response->getErrorCode());
        $this->assertSame('test error message', $response->getErrorMessage());
    }
}