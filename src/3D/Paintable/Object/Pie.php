<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;
use Image3D\Paintable\Polygon;

/**
 * Image_3D_Object_Pie
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
class Pie extends \Image3D\Paintable\Base3DObject
{

    /**
     *
     * @param array $parameter ['inside' => 0|1]
     */
    public function __construct(array $parameter)
    {
        $parameter['inside'] = isset($parameter['inside']) ? $parameter['inside'] : 0;
        
        $parameter = $this->checkParameter($parameter);
        
        if ($parameter['inside'] == 0) {
            $this->createPie($parameter);
        } else {
            $this->createDonutPie($parameter);
        }
    }

    /**
     *
     * @param array $parameter ['end' => .., 'start' => .., 'detail' => .., 'outside' => .., 'inside' => ..]
     */
    protected function createPie(array $parameter)
    {
        $step = ($parameter['end'] - $parameter['start']) / $parameter['detail'];

        // center
        $centerTop = new Point(0, 0, .5);
        $centerBottom = new Point(0, 0, -.5);

        // Add polygones for top and bottom of the pie
        $x = cos($parameter['start']) * $parameter['outside'];
        $y = sin($parameter['start']) * $parameter['outside'];
        $top = new Point($x, $y, .5);
        $bottom = new Point($x, $y, -.5);

        // Polygones for the opening side
        $this->addPolygon(new Polygon($top, $centerTop, $centerBottom));
        $this->addPolygon(new Polygon($bottom, $top, $centerBottom));

        for ($i = 1; $i <= $parameter['detail']; $i++) {
            $x = cos($parameter['start'] + $i * $step) * $parameter['outside'];
            $y = sin($parameter['start'] + $i * $step) * $parameter['outside'];

            $newTop = new Point($x, $y, .5);
            $newBottom = new Point($x, $y, -.5);

            $this->addPolygon(new Polygon($centerTop, $top, $newTop));
            $this->addPolygon(new Polygon($centerBottom, $bottom, $newBottom));

            // Rand
            $this->addPolygon(new Polygon($top, $newBottom, $newTop));
            $this->addPolygon(new Polygon($top, $bottom, $newBottom));

            $top = $newTop;
            $bottom = $newBottom;
        }

        // Polygones for the closing side
        $this->addPolygon(new Polygon($top, $centerTop, $centerBottom));
        $this->addPolygon(new Polygon($bottom, $top, $centerBottom));
    }

    /**
     *
     * @param array $array
     * @return array
     */
    protected function checkParameter(array $array): array
    {
        $array['detail'] = max(1, (int) $array['detail']);
        $array['outside'] = max(0, (int) $array['outside']);
        $array['inside'] = min(max(0, (int) $array['inside']), $array['outside']);
        $array['start'] = max(0, deg2rad((float) $array['start']));
        $array['end'] = max(0, deg2rad((float) $array['end']));

        return $array;
    }

    /**
     *
     * @param array $parameter ['end' => .., 'start' => .., 'detail' => .., 'outside' => .., 'inside' => ..]
     */
    public function createDonutPie($parameter)
    {
    }
}
