<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;
use Image3D\Paintable\Polygon;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Cube extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var array
     */
    protected $_points = [];

    /**
     * @param array $parameter
     */
    public function __construct(array $parameter = [])
    {
        $x = (float) $parameter[0] ?? 0;
        $y = (float) $parameter[1] ?? 0;
        $z = (float) $parameter[2] ?? 0;

        // links unten unten
        $this->_points[1] = new Point(-$x / 2, -$y / 2, -$z / 2);
        // links unten oben
        $this->_points[2] = new Point(-$x / 2, -$y / 2, $z / 2);

        // links oben unten
        $this->_points[3] = new Point(-$x / 2, $y / 2, -$z / 2);
        // links oben oben
        $this->_points[4] = new Point(-$x / 2, $y / 2, $z / 2);

        // rechts unten unten
        $this->_points[5] = new Point($x / 2, -$y / 2, -$z / 2);
        // rechtes unten oben
        $this->_points[6] = new Point($x / 2, -$y / 2, $z / 2);

        // rechts oben unten
        $this->_points[7] = new Point($x / 2, $y / 2, -$z / 2);
        // rechts oben oben
        $this->_points[8] = new Point($x / 2, $y / 2, $z / 2);

        // Oben & Unten
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[4], $this->_points[8]));
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[8], $this->_points[7]));
//
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[1], $this->_points[6]));
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[5], $this->_points[6]));
//
//        // Links & Rechts
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[2], $this->_points[4]));
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[1], $this->_points[2]));
//
//        $this->addPolygon(new Polygon($this->_points[8], $this->_points[5], $this->_points[7]));
//        $this->addPolygon(new Polygon($this->_points[8], $this->_points[6], $this->_points[5]));
//
//        // Rueck- & Frontseite
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[8], $this->_points[4]));
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[6], $this->_points[8]));
//
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[7], $this->_points[5]));
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[3], $this->_points[7]));
        
        #############################################################################################################
        # use 4 points instead of 3
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[4], $this->_points[8], $this->_points[3]));
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[8], $this->_points[7], $this->_points[3]));
//
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[1], $this->_points[6], $this->_points[2]));
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[5], $this->_points[6], $this->_points[1]));
//
//        // Links & Rechts
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[2], $this->_points[4], $this->_points[3]));
//        $this->addPolygon(new Polygon($this->_points[3], $this->_points[1], $this->_points[2], $this->_points[3]));
//
//        $this->addPolygon(new Polygon($this->_points[8], $this->_points[5], $this->_points[7], $this->_points[8]));
//        $this->addPolygon(new Polygon($this->_points[8], $this->_points[6], $this->_points[5], $this->_points[8]));
//
//        // Rueck- & Frontseite
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[8], $this->_points[4], $this->_points[2]));
//        $this->addPolygon(new Polygon($this->_points[2], $this->_points[6], $this->_points[8], $this->_points[2]));
//
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[7], $this->_points[5], $this->_points[1]));
//        $this->addPolygon(new Polygon($this->_points[1], $this->_points[3], $this->_points[7], $this->_points[1]));
        
        #############################################################################################################
        # use rectangles instead of triangles -> results in much better results when using perspectively-renderer
        // unten
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[4], $this->_points[8], $this->_points[7]));
        // oben
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[1], $this->_points[5], $this->_points[6]));
        // links
        $this->addPolygon(new Polygon($this->_points[1], $this->_points[2], $this->_points[4], $this->_points[3]));
        // rechts
        $this->addPolygon(new Polygon($this->_points[5], $this->_points[6], $this->_points[8], $this->_points[7]));
        // vorn
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[6], $this->_points[8], $this->_points[4]));
        // hinten
        $this->addPolygon(new Polygon($this->_points[1], $this->_points[5], $this->_points[7], $this->_points[3]));
    }

    /**
     * @param int $index
     * @return false|Point
     */
    public function getPoint($index)
    {
        return isset($this->_points[$index]) ? $this->_points[$index] : false;
    }
}
