<?php
namespace LocationBundle\Tests\Service;

use LocationBundle\Location\Location;
use LocationBundle\Service\Client;
use LocationBundle\Service\Response;

/**
 * Class ClientTest
 * @package LocationBundle\Tests\Service
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function testLocationExample()
    {
        $curl = $this->getMock('Guzzle\\Http\\Client', array(), array(), '', false, false);
        $client = new Client($curl);

        $response = $this->getMock('Guzzle\\Http\\Message\\Response', array(), array(), '', false, false);
        $response->expects($this->once())
            ->method('json')
            ->will($this->returnValue([
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
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $request = $this->getMock('Guzzle\\Http\\Message\\RequestInterface', array(), array(), '', false, false);
        $request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $curl->expects($this->once())
            ->method('get')
            ->with('/locations')
            ->will($this->returnValue($request));

        foreach ($client->getLocations() as $location) {
            $this->assertInstanceOf('LocationBundle\\Location\\Location', $location);
            $this->assertSame($location->getName(), 'Eiffel Tower');
            $this->assertInstanceOf('LocationBundle\\Location\\Coordinates', $location->getCoordinates());
            $this->assertSame($location->getCoordinates()->getLat(), 21.12);
            $this->assertSame($location->getCoordinates()->getLng(), 19.56);
        }
    }
}