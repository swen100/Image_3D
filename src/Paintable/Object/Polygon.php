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
    /**
     * @param array{\Image3D\Point} $points
     */
    public function __construct(array $points = [])
    {
        $polygon = new PaintablePolygon();
        foreach ($points as $point) {
            $polygon->addPoint($point);
        }
        $this->addPolygon($polygon);
    }
    
    /**
     * @param \Image3D\Point $pointObj
     */
    public function addPoint(\Image3D\Point $pointObj)
    {
        $this->getPolygon()->addPoint($pointObj);
    }

    /**
     * @return \Image3D\Paintable\Polygon
     */
    public function getPolygon(): Polygon
    {
        return reset($this->_polygones);
    }
}
