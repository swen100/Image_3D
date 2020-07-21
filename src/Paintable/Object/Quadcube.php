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
class Quadcube extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var array
     */
    protected $_points = [];

    public function __construct(array $parameter = [])
    {
        $x = (float) $parameter[0] ?? 0;
        $y = (float) $parameter[1] ?? 0;
        $z = (float) $parameter[2] ?? 0;

        $this->_points[1] = new Point(-$x / 2, -$y / 2, -$z / 2);
        $this->_points[2] = new Point(-$x / 2, -$y / 2, $z / 2);

        $this->_points[3] = new Point(-$x / 2, $y / 2, -$z / 2);
        $this->_points[4] = new Point(-$x / 2, $y / 2, $z / 2);

        $this->_points[5] = new Point($x / 2, -$y / 2, -$z / 2);
        $this->_points[6] = new Point($x / 2, -$y / 2, $z / 2);

        $this->_points[7] = new Point($x / 2, $y / 2, -$z / 2);
        $this->_points[8] = new Point($x / 2, $y / 2, $z / 2);

        // Oben & Unten
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[4], $this->_points[8], $this->_points[7]));
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[1], $this->_points[5], $this->_points[6]));

        // Links & Rechts
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[1], $this->_points[2], $this->_points[4]));
        $this->addPolygon(new Polygon($this->_points[8], $this->_points[6], $this->_points[5], $this->_points[7]));

        // Rueck- & Frontseite
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[6], $this->_points[8], $this->_points[4]));
        $this->addPolygon(new Polygon($this->_points[1], $this->_points[3], $this->_points[7], $this->_points[5]));
    }
}
