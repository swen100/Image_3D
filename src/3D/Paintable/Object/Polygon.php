<?php
namespace Image3D\Paintable\Object;

use Image3D\Paintable\Polygon as PaintablePolygon;

/**
 * Image_3D_Object_Polygon
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_3D
 * @since      Class available since Release 0.1.0
 */
class Polygon extends \Image3D\Paintable\Base3DObject
{
    public function __construct($points)
    {
        parent::__construct();

        $polygon = new PaintablePolygon();
        foreach ($points as $point) {
            $polygon->addPoint($point);
        }
        $this->_addPolygon($polygon);
    }

    public function getPolygon()
    {
        return reset($this->_polygones);
    }
}
