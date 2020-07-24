<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;

use Image3D\Paintable\Polygon;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Swen Zanon <swen.zanon@geoglis.de>
 * @copyright 2020 Swen Zanon
 * @link      http://pear.php.net/package/Image_3D
 */
class Building extends \Image3D\Paintable\Base3DObject
{
    public function __construct(array $params = [])
    {
        $points = $params['points'] ?? [];
        $height = $params['height'] ?? 1;

        // sides
        $numPoints = count($points);
        foreach ($points as $i => $pt) {
            /** @var \Image3D\Point $point */
            $i_nxt = ($i + 1) % $numPoints;

            $polygon = new Polygon([
                new Point($pt[0], $pt[1], 0),
                new Point($points[$i_nxt][0], $points[$i_nxt][1], 0),
                new Point($points[$i_nxt][0], $points[$i_nxt][1], $height),
                new Point($pt[0], $pt[1], $height)
            ]);
            $this->addPolygon($polygon);
        }
        
        // bottom
        $polyPoints = [];
        foreach ($points as $pt) {
            $polyPoints[] = new Point($pt[0], $pt[1], $height);
        }
        $this->addPolygon(new Polygon($polyPoints));
        
        // top
        $polyPoints = [];
        foreach ($points as $pt) {
            $polyPoints[] = new Point($pt[0], $pt[1], 0);
        }
        $this->addPolygon(new Polygon($polyPoints));
        
    }
}
