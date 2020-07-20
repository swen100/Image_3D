<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;
use Image3D\Paintable\Polygon;

/**
 * Image_3D_Object_Sphere
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
class Torus extends \Image3D\Paintable\Base3DObject
{

    public function __construct($options)
    {
        $inner_radius = (float) $options['inner_radius'];
        $outer_radius = (float) $options['outer_radius'];

        $r1 = ($outer_radius - $inner_radius) / 2;
        $r2 = $inner_radius + $r1;

        $d1 = (int) round(max(1, $options['detail_1']) * 4);
        $d2 = (int) round(max(1, $options['detail_2']) * 4);

        $rings = array();
        for ($i = 0; $i < $d1; ++$i) {
            $rings[$i] = array();
            for ($j = 0; $j < $d2; ++$j) {
                $_i = $i / $d1;
                $_j = $j / $d2;

                $z = cos($_j * pi() * 2) * $r1;
                $z2 = sin($_j * pi() * 2) * $r1;

                $x = ($r2 + $z2) * cos($_i * pi() * 2);
                $y = ($r2 + $z2) * sin($_i * pi() * 2);

                $rings[$i][] = new Point($x, $y, $z);
            }
        }

        foreach ($rings as $i => $ring) {
            $i_next = ($i + 1) % count($rings);
            foreach ($ring as $j => $point) {
                $j_next = ($j + 1) % count($ring);

                $this->addPolygon(new Polygon($rings[$i_next][$j], $rings[$i][$j], $rings[$i][$j_next]));
                $this->addPolygon(new Polygon($rings[$i_next][$j], $rings[$i][$j_next], $rings[$i_next][$j_next]));
            }
        }
    }
}
