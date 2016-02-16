<?php

namespace MeetMe;

class GeoPointTest extends \PHPUnit_Framework_TestCase
{

    private $lonDegrees = -81.9651;
    private $latDegrees = 33.5018;
    private $latRad = 0.5847167153446363;
    private $lonRad = -1.4305608667264043;
    private $format = GeoPoint::DEGREES;

    /**
     * @var GeoPoint
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new GeoPoint($this->latDegrees, $this->lonDegrees, $this->format);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Latitude must be numeric
     */
    public function testInvalidLatitudeNumeric()
    {
        new GeoPoint('bogus', -82, GeoPoint::DEGREES);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Latitude out of bounds
     */
    public function testInvalidLatitudeOutOfBounds()
    {
        new GeoPoint((M_PI / 2) * 5, -82, GeoPoint::RADIANS);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Longitude must be numeric
     */
    public function testInvalidLongitudeNumeric()
    {
        new GeoPoint($this->latDegrees, 'bogus', GeoPoint::DEGREES);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Longitude out of bounds
     */
    public function testInvalidLongitudeOutOfBounds()
    {
        new GeoPoint((M_PI / 2), M_PI + 1, GeoPoint::RADIANS);
    }

    /**
     * @covers GeoPoint::setOutputFormat
     * @covers GeoPoint::getOutputFormat
     */
    public function testSetOutputFormat()
    {
        $this->instance->setOutputFormat(GeoPoint::DEGREES);
        $this->assertSame($this->instance->getOutputFormat(), GeoPoint::DEGREES);

        $this->instance->setOutputFormat(GeoPoint::RADIANS);
        $this->assertSame($this->instance->getOutputFormat(), GeoPoint::RADIANS);
    }


    /**
     * @covers GeoPoint::getLatitude
     */
    public function testGetLatitude()
    {
        $this->assertSame(
            $this->latDegrees,
            $this->instance->getLatitude()
        );

        $this->instance->setOutputFormat(GeoPoint::RADIANS);
        $this->assertSame(
            $this->latRad,
            $this->instance->getLatitude()
        );
    }

    /**
     * @covers GeoPoint::getLongitude
     */
    public function testGetLongitude()
    {
        $this->assertSame(
            $this->lonDegrees,
            $this->instance->getLongitude()
        );

        $this->instance->setOutputFormat(GeoPoint::RADIANS);
        $this->assertSame(
            $this->lonRad,
            $this->instance->getLongitude()
        );
    }

    /**
     * @covers GeoPoint::convertRadiansToDegrees
     */
    public function testConvertRadiansToDegrees()
    {
        $this->assertSame(
            $this->lonDegrees,
            $this->instance->convertRadiansToDegrees($this->lonRad)
        );
    }

    /**
     * @covers GeoPoint::convertDegreesToRadians
     */
    public function testConvertDegreesToRadians()
    {
        $this->assertSame(
            $this->latRad,
            $this->instance->convertDegreesToRadians($this->latDegrees)
        );
    }

    /**
     * @covers GeoPoint::convertMilesToKilometers
     */
    public function testConvertMilesToKilometers()
    {
        $this->assertSame(
            8.046719999999999,
            $this->instance->convertMilesToKilometers(5)
        );
    }

    /**
     * @covers GeoPoint::convertKilometersToMiles
     */
    public function testConvertKilometersToMiles()
    {
        $this->assertSame(
            5.0,
            $this->instance->convertKilometersToMiles(8.04672)
        );
    }

    /**
     * @covers GeoPoint::distanceTo
     */
    public function testDistanceTo()
    {
        $geo = new GeoPoint(34.5303, -82.5198, GeoPoint::DEGREES);
        $this->assertSame(
            '125.27085148657',
            (string) $this->instance->distanceTo($geo, GeoPoint::KILOMETERS)
        );
    }

    /**
     * @covers GeoPoint::boundingCoordinates
     */
    public function testBoundingCoordinatesWithoutRadius()
    {
        $expected = array(
            new GeoPoint(33.456833990283265, -82.01902458488057, GeoPoint::DEGREES),
            new GeoPoint(33.54676600971674, -81.91117541511944, GeoPoint::DEGREES)
        );

        $this->assertEquals(
            $expected,
            $this->instance->boundingCoordinates(5)
        );
    }

    /**
     * @covers GeoPoint::boundingCoordinates
     */
    public function testBoundingCoordinatesWithRadius()
    {
        $expected = array(
            new GeoPoint(33.456826860664776, -82.01903313493325, GeoPoint::DEGREES),
            new GeoPoint(33.54677313933523, -81.91116686506675, GeoPoint::DEGREES)
        );

        $this->assertEquals(
            $expected,
            $this->instance->boundingCoordinates(5, 6370)
        );
    }

    public function tearDown()
    {
        $this->instance = null;
    }
}
