<?php

namespace MeetMe;

use RuntimeException;

/**
 * Represents a point on the surface of the Earth.
 *
 * @package MeetMe
 * @author MeetMe <oss@meetme.com>
 */
class GeoPoint
{
    /**
     * Constants and statics used throughout
     */
    const KILOMETERS_PER_MILE = 1.6093439999999999;
    const MILES_PER_KILOMETER = 0.621371192237334;
    const EARTH_RADIUS_KM = 6371.01;
    const EARTH_RADIUS_MI = 3958.762079;
    const RADIANS = 'radian';
    const DEGREES = 'degrees';
    const MILES = 'miles';
    const KILOMETERS = 'kilometers';
    const RADIANS_PER_DEGREE = 0.0174532925199433;
    const DEGREES_PER_RADIAN = 57.295779513082321;

    // JHH - MAX_RADIANS should be defined as a constant and validated against for radians
    // JHH - MIN_RADIANS should be defined as a constant and validated against for radians
    // JHH - MAX_DEGREES should be defined as a constant and validated against for degrees
    // JHH - MIN_DEGREES should be defined as a constant and validated against for degrees
    protected $maxLatitude;
    protected $minLatitude;
    protected $maxLongitude = M_PI;
    protected $minLogitude;
    protected $fullCircleRad;

    /**
     * Latitude in degrees
     *
     * @var float|int
     */
    private $degLat;

    /**
     * Longitude in degrees
     *
     * @var float|int
     */
    private $degLon;

    /**
     * Latitude in radians
     *
     * @var float|int
     */
    private $radLat;

    /**
     * Longitude in radians
     *
     * @var float|int
     */
    private $radLon;

    /**
     * Return format of the values
     *
     * @var string
     */
    private $returnFormat = self::DEGREES;

    /**
     * GeoPoint constructor.
     *
     * @param int|float $lat
     * @param int|float $lon
     * @param bool $format
     */
    public function __construct($lat, $lon, $format)
    {
        $this->maxLatitude = (M_PI / 2);
        $this->minLatitude = -$this->maxLatitude;
        $this->minLogitude = -$this->maxLongitude;
        $this->fullCircleRad = M_PI * 2;

        if ($format === self::RADIANS) {
            $this->degLat = $this->convertRadiansToDegrees($lat);
            $this->degLon = $this->convertRadiansToDegrees($lon);
            $this->radLat = $lat;
            $this->radLon = $lon;
        } else if ($format === self::DEGREES) {
            $this->degLat = $lat;
            $this->degLon = $lon;
            $this->radLat = $this->convertDegreesToRadians($lat);
            $this->radLon = $this->convertDegreesToRadians($lon);
        } else {
            throw new RuntimeException(
                'Invalid input format. Expected radians or degrees'
            );
        }

        $this->validateParameters($lat, $lon);
    }

    /**
     * Sets the output format for values. Options are
     * GeoPoint::RADIANS or GeoPoint::DEGREES.
     *
     * @param string $outputFormat
     * @return void
     */
    public function setOutputFormat($outputFormat) {
        if (self::RADIANS === $outputFormat) {
            $this->returnFormat = self::RADIANS;
        } else if (self::DEGREES === $outputFormat) {
            $this->returnFormat = self::DEGREES;
        } else {
          throw new RuntimeException(
            'Output format must be GeoPoint::RADIANS or GeoPoint::DEGREES');
        }
    }

    /**
     * Gets the current output format for values
     *
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->returnFormat;
    }

    /**
     * Converts radians to degrees
     *
     * @param int|float $value
     * @return int|float
     */
    public static function convertRadiansToDegrees($value)
    {
// JHH - Validate the passed-in value?
/*
        if (-M_PI > $value || M_PI < $value) {
            throw new RuntimeException(sprintf(
              'Value [%s] not in range of [%g, %g]',
              print_r($value, true), -M_PI, M_PI));
        }
*/

        return ($value * self::DEGREES_PER_RADIAN);
    }

    /**
     * Converts degrees to radians
     *
     * @param int|float $value
     * @return int|float
     */
    public static function convertDegreesToRadians($value)
    {
        return ($value * self::RADIANS_PER_DEGREE);
    }

    /**
     * Converts miles to kilometers
     *
     * @param int|float $value
     * @return int|float
     */
    public static function convertMilesToKilometers($value)
    {
        return ($value * self::KILOMETERS_PER_MILE);
    }

    /**
     * Converts kilometers to miles
     *
     * @param int|float $value
     * @return int|float
     */
    public static function convertKilometersToMiles($value)
    {
        return ($value * self::MILES_PER_KILOMETER);
    }

    /**
     * Gets the value for latitude based on the
     * format specified by setOutputFormat
     *
     * @see \MeetMe\GeoPoint::setOutputFormat
     * @return float|int
     */
    public function getLatitude()
    {
        if (self::RADIANS === $this->returnFormat) {
            return $this->radLat;
        }

        return $this->degLat;
    }

    /**
     * Gets the vlaue for longitude based on the
     * format specified by setOutputFormat
     *
     * @see \MeetMe\GeoPoint::setOutputFormat
     * @return float|int
     */
    public function getLongitude()
    {
        if (self::RADIANS === $this->returnFormat) {
            return $this->radLon;
        }

        return $this->degLon;
    }

    /**
     * Calculate the distance between two points
     *
     * @param GeoPoint $point
     * @param string $kmOrMiles
     * @return float
     */
    public function distanceTo(GeoPoint $point, $kmOrMiles) {
        $radius = (self::KILOMETERS === $kmOrMiles) ?
            self::EARTH_RADIUS_KM : self::EARTH_RADIUS_MI;

        $lat1 = $this->radLat;
        $lat2 = $point->radLat;
        $lon1 = $this->radLon;
        $lon2 = $point->radLon;

        $retVal = acos(
            sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) *
            cos($lon1 - $lon2)
        );

        $retVal *= $radius;
        return $retVal;
    }

    /**
     * Calculates the bounding coordinates
     *
     * @param int|float $distance
     * @param int|float $radius
     * @param string $kmOrMiles
     * @return GeoPoint[]
     */
    public function boundingCoordinates($distance, $radius = null, $kmOrMiles = self::KILOMETERS) {
        if (false === is_numeric($distance)) {
            throw new RuntimeException('Invalid value for $distance. Must be numeric');
        }

        if (false === is_numeric($radius) || 0 > $radius) {
            $radius = (self::KILOMETERS === $kmOrMiles) ?
                self::EARTH_RADIUS_KM : self::EARTH_RADIUS_MI;
        }

        $lat = $this->radLat;
        $lon = $this->radLon;
        $radDistance = ($distance / $radius);
        $minLat = ($lat - $radDistance);
        $maxLat = ($lat + $radDistance);

        if ($minLat > $this->minLatitude && $maxLat < $this->maxLatitude) {
            $deltaLon = asin(sin($radDistance) / cos($lat));
            $minLon = $lon - $deltaLon;

            if ($minLon < $this->minLogitude) {
                $minLon += $this->fullCircleRad;
            }

            $maxLon = $lon + $deltaLon;

            if ($maxLon > $this->maxLongitude) {
                $maxLon -= $this->fullCircleRad;
            }
        } else {
            $minLat = max($minLat, $this->minLatitude);
            $maxLat = min($maxLat, $this->maxLatitude);
            $minLon = $this->minLogitude;
            $maxLon = $this->maxLongitude;
        }

        return array(
            $this->getGeoPoint($minLat, $minLon, self::RADIANS),
            $this->getGeoPoint($maxLat, $maxLon, self::RADIANS)
        );
    }

    /**
     * Stubbed method that gets a new instance of GeoPoint
     *
     * @param int|float $lat
     * @param int|float $lon
     * @param string $format
     * @return GeoPoint
     * @codeCoverageIgnore
     */
    protected function getGeoPoint($lat, $lon, $format)
    {
        return new GeoPoint($lat, $lon, $format);
    }

    /**
     * Validates the input parameters from the constructor
     *
     * @param int|float $lat
     * @param int|float $lon
     */
    protected function validateParameters($lat, $lon)
    {
        if (!is_numeric($lat)) {
            throw new RuntimeException('Latitude must be numeric');
        }

        if (!is_numeric($lon)) {
            throw new RuntimeException('Longitude must be numeric');
        }

        if ($this->radLat < $this->minLatitude || $this->radLat > $this->maxLatitude) {
            throw new RuntimeException('Latitude out of bounds');
        }

        if ($this->radLon < $this->minLatitude || $this->radLon > $this->maxLatitude) {
            throw new RuntimeException('Longitude out of bounds');
        }
    }
}
