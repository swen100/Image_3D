<?php
namespace Image3D\Paintable\Object;

use Image3D\Paintable\Polygon as PaintablePolygon;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Polygon extends \Image3D\Paintable\Base3DObject
{
    public function __construct($points)
    {
        $polygon = new PaintablePolygon();
        foreach ($points as $point) {
            $polygon->addPoint($point);
        }
        $this->addPolygon($polygon);
    }

    public function getPolygon()
    {
        return reset($this->_polygones);
    }
}
