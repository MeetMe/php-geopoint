# Geographic Point

GeoPoint represents a geographic point for PHP including conversion, point-to-point distance, and bounding-box calculations.

## Usage

```
$statueOfLibertyPoint = new GeoPoint(40.689604, -74.04455, GeoPoint::DEGREES);
```

## Constructor options

* `latitude` -- Latitude
* `longitude` -- Longitude
* `format` -- The format of the passed-in lat/lon as GeoPoint::DEGREES or GeoPoint::RADIANS

## Methods

* `getLatitude()`: Return the point's latitude per the format of the point (GeoPoint::DEGREES or GeoPoint::RADIANS)
* `getLongitude()`: Return the point's longitude per the format of the point (GeoPoint::DEGREES or GeoPoint::RADIANS)
* `distanceTo(GeoPoint, kmOrMiles)`: Calculate the distance to another `GeoPoint` instance using either GeoPoint::KILOMETERS or GeoPoint::MILES.
* `boundingCoordinates(distance, radius, kmOrMiles)`: Calculates the bounding coordinates of `distance` from the point and returns an array with the SW and NE points of the bounding box . If `radius` is not provided, the radius of the Earth will be used. The distance is calculated in kilometers unless kmOrMiles is set to GeoPoint::MILES.

## Static Methods

* `GeoPoint::degreesToRadians(value)`: Converts `value` in degrees to radians
* `GeoPoint::radiansToDegrees(value)`: Converts `value` in radians to degrees
* `GeoPoint::milesToKilometers(value)`: Converts `value` in miles to kilometers
* `GeoPoint::kilometersToMiles(value)`: Converts `value` in kilometers to miles

## Running Tests

`php composer.phar test`
 
# Credits

## Background

This is inspired by [David Wood's JavaScript port](https://github.com/davidwood/node-geopoint) of the Java code presented in [Finding Points Within a Distance of a Latitude/Longitude Using Bounding Coordinates](http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates) by [Jan Philip Matuschek](http://janmatuschek.de/Contact).

## PHP Implementation Authors/Contributors

* Will Fitch (<wfitch@meetme.com>)
* Jonah H. Harris (<jonah.harris@meetme.com>)

## License

    The MIT License
    
    Copyright (c) 2016 MeetMe, Inc. http://www.meetmecorp.com/
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.

