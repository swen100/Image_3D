<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;
use Image3D\Paintable\Polygon;

class Cone extends \Image3D\Paintable\Base3DObject
{

    public function __construct($parameter)
    {
        #$radius = 1;
        $height = 1;
        $detail = max(3, (int) $parameter['detail']);

        // Generate points according to parameters
        $top = new Point(0, $height, 0);
        $bottom = new Point(0, 0, 0);

        $last = new Point(1, 0, 0);
        $points[] = $last;

        for ($i = 1; $i <= $detail; ++$i) {
            $actual = new Point(cos(deg2rad(360 * $i / $detail)), 0, sin(deg2rad(360 * $i / $detail)));
            $points[] = $actual;

            // Build polygon
            $this->addPolygon(new Polygon($top, $last, $actual));
            $this->addPolygon(new Polygon($bottom, $last, $actual));
            $last = $actual;
        }

        // Build closing polygon
        $this->addPolygon(new Polygon($top, $last, $points[0]));
        $this->addPolygon(new Polygon($bottom, $last, $points[0]));
    }
}
