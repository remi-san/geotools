<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests;

use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AddressFactory;
use League\Geotools\Batch\BatchGeocoded;
use League\Geotools\Coordinate\Ellipsoid;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return GeocoderInterface
     */
    protected function getStubGeocoder()
    {
        $stub = $this
            ->getMockBuilder('\Geocoder\Geocoder')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param array $providers
     * @param array $data
     *
     * @return GeocoderInterface
     */
    protected function getMockGeocoderReturns(array $providers, array $data = array())
    {
        $addresses = new AddressCollection();

        if (!empty($data)) {
            $addresses = (new AddressFactory())->createFromArray([ $data ]);
        }

        $mock = $this->getMock('\Geocoder\ProviderAggregator');
        $mock
            ->expects($this->any())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->any())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->returnValue($addresses));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->returnValue($addresses));

        return $mock;
    }

    /**
     * @param array $providers
     *
     * @return GeocoderInterface
     */
    protected function getMockGeocoderThrowException(array $providers)
    {
        $mock = $this->getMock('\Geocoder\ProviderAggregator');
        $mock
            ->expects($this->once())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->any())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->throwException(new \Exception));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->throwException(new \Exception));

        return $mock;
    }

    /**
     * @return CoordinateInterface
     */
    protected function getStubCoordinate()
    {
        $stub = $this
            ->getMockBuilder('\League\Geotools\Coordinate\CoordinateInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param array $coordinate
     * @param Ellipsoid $ellipsoid
     *
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate, Ellipsoid $ellipsoid = null)
    {
        $mock = $this->getMock('\League\Geotools\Coordinate\CoordinateInterface');
        $mock
            ->expects($this->any())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate[0]));
        $mock
            ->expects($this->any())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate[1]));

        if ($ellipsoid) {
            $mock
                ->expects($this->atLeastOnce())
                ->method('getEllipsoid')
                ->will($this->returnValue($ellipsoid));
        }

        return $mock;
    }

    /**
     * @param $expects
     *
     * @return AddressCollection
     */
    protected function getMockGeocoded($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $mock = $this->getMock('\League\Geotools\Batch\BatchGeocoded');
        $mock
            ->expects($expects)
            ->method('getCoordinates')
            ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * @param array $coordinate
     *
     * @return AddressCollection
     */
    protected function getMockGeocodedReturns(array $coordinate)
    {
        $mock = $this->getMock('\League\Geotools\Batch\BatchGeocoded');
        $mock
            ->expects($this->atLeastOnce())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate['latitude']));
        $mock
            ->expects($this->atLeastOnce())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate['longitude']));

        return $mock;
    }

    /**
     * @return BatchGeocoded
     */
    protected function getStubBatchGeocoded()
    {
        $stub = $this
            ->getMockBuilder('\League\Geotools\Batch\BatchGeocoded')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @return CacheInterface
     */
    protected function getStubCache()
    {
        $stub = $this
            ->getMockBuilder('\League\Geotools\Cache\CacheInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param string $method
     * @param $returnValue
     *
     * @return CacheInterface
     */
    protected function getMockCacheReturns($method, $returnValue)
    {
        $mock = $this->getMock('\League\Geotools\Cache\CacheInterface');
        $mock
            ->expects($this->atLeastOnce())
            ->method($method)
            ->will($this->returnValue($returnValue));

        return $mock;
    }

	/**
     * Create an address object for testing
     *
     * @param array $data
     * @return Address|null
     */
    protected function createAddress(array $data)
    {
        $addresses = (new AddressFactory())->createFromArray([ $data ]);
        return 0 === count($addresses) ? null : $addresses->first();
    }

	/**
     * Create an empty address object
     *
     * @return Address|null
     */
    protected function createEmptyAddress()
    {
        return $this->createAddress([]);
    }
}
