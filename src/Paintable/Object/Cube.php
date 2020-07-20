<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;
use Image3D\Paintable\Polygon;

/**
 * Image_3D_Object_Cube
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @license   http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_3D
 * @since     Class available since Release 0.1.0
 */
class Cube extends \Image3D\Paintable\Base3DObject
{

    protected $_points;

    public function __construct($parameter)
    {
        $x = (float) $parameter[0];
        $y = (float) $parameter[1];
        $z = (float) $parameter[2];

        $this->_points = array();

        $this->_points[1] = new Point(-$x / 2, -$y / 2, -$z / 2);
        $this->_points[2] = new Point(-$x / 2, -$y / 2, $z / 2);

        $this->_points[3] = new Point(-$x / 2, $y / 2, -$z / 2);
        $this->_points[4] = new Point(-$x / 2, $y / 2, $z / 2);

        $this->_points[5] = new Point($x / 2, -$y / 2, -$z / 2);
        $this->_points[6] = new Point($x / 2, -$y / 2, $z / 2);

        $this->_points[7] = new Point($x / 2, $y / 2, -$z / 2);
        $this->_points[8] = new Point($x / 2, $y / 2, $z / 2);

        // Oben & Unten
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[4], $this->_points[8]));
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[8], $this->_points[7]));

        $this->addPolygon(new Polygon($this->_points[2], $this->_points[1], $this->_points[6]));
        $this->addPolygon(new Polygon($this->_points[1], $this->_points[5], $this->_points[6]));

        // Links & Rechts
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[2], $this->_points[4]));
        $this->addPolygon(new Polygon($this->_points[3], $this->_points[1], $this->_points[2]));

        $this->addPolygon(new Polygon($this->_points[8], $this->_points[5], $this->_points[7]));
        $this->addPolygon(new Polygon($this->_points[8], $this->_points[6], $this->_points[5]));

        // Rueck- & Frontseite
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[8], $this->_points[4]));
        $this->addPolygon(new Polygon($this->_points[2], $this->_points[6], $this->_points[8]));

        $this->addPolygon(new Polygon($this->_points[1], $this->_points[7], $this->_points[5]));
        $this->addPolygon(new Polygon($this->_points[1], $this->_points[3], $this->_points[7]));
    }

    public function getPoint($int)
    {
        return isset($this->_points[$int]) ? $this->_points[$int] : false;
    }
}
