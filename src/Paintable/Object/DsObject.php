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
class DsObject extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var array
     */
    protected $_points = [];

    public function __construct()
    {
    }
    
    /**
     *
     * @return array
     */
    public function getPoints(): array
    {
        return $this->_points;
    }
    
    /**
     *
     * @return int
     */
    public function getNumPoints(): int
    {
        return count($this->_points);
    }

    public function newPoint($x, $y, $z)
    {
        $this->_points[] = new Point($x, $y, $z);
//        echo "New Point: $x, $y, $z -> ", count($this->_points), "\n";
    }

    public function newPolygon($p1, $p2, $p3)
    {
        if (!isset($this->_points[$p1]) || !isset($this->_points[$p2]) || !isset($this->_points[$p3])) {
//            printf("ERROR: Unknown point (%d, %d, %d of %d)\n", $p1, $p2, $p3, count($this->_points) - 1);
            return false;
        }
        $this->addPolygon(new Polygon($this->_points[$p1], $this->_points[$p2], $this->_points[$p3]));
//        echo "New Polygon: $p1, $p2, $p3 -> ", count($this->_polygones), "\n";
    }

    public function debug()
    {
        printf("Points: %d | Polygones: %d (%d)\n", count($this->_points), count($this->_polygones), $this->getPolygonCount());
    }
}
