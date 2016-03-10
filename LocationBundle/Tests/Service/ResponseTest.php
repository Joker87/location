<?php
namespace LocationBundle\Tests\Service;

use LocationBundle\Service\Location;
use LocationBundle\Service\LocationServiceException;
use LocationBundle\Service\Response;
use LocationBundle\Service\ServiceClient;

/**
 * Class ResponseTest
 * @package LocationBundle\Tests\Service
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseExample()
    {
        $response = new Response(json_encode([
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
        ]));

        /** @var Location $location */
        foreach ($response as $location) {
            $this->assertInstanceOf('LocationBundle\\Service\\Location', $location);
            $this->assertSame($location->getName(), 'Eiffel Tower');
            $this->assertInstanceOf('LocationBundle\\Service\\Coordinates', $location->getCoordinates());
            $this->assertSame($location->getCoordinates()->getLat(), 21.12);
            $this->assertSame($location->getCoordinates()->getLng(), 19.56);
        }
    }

    public function testResponseError()
    {
        $response = new Response(json_encode([
            'data' => [
                'message' => 'test error message',
                'code' => 'error code test',
            ],
            'success' => false,
        ]));
        $this->assertNull($response->getLocation(0));
        $this->assertFalse($response->isSuccess());
        $this->assertSame('error code test', $response->getErrorCode());
        $this->assertSame('test error message', $response->getErrorMessage());
    }
}