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
class Torus extends \Image3D\Paintable\Base3DObject
{

    /**
     * @param array $options [inner_radius:float, outer_radius:float, detail_1:float, detail_2:float]
     * inner_radius -> default 20
     * outer_radius -> default 40
     * detail_1 -> number of "horizontal" segments, default 10
     * detail_2 -> number of "vertical" segments, default 5
     */
    public function __construct(array $options = [])
    {
        $inner_radius = (float) ($options['inner_radius'] ?? 20.0);
        $outer_radius = (float) ($options['outer_radius'] ?? 40.0);

        $d1 = (int) round(max(1, $options['detail_1'] ?? 10.0) * 4);
        $d2 = (int) round(max(1, $options['detail_2'] ?? 5.0) * 4);

        $r1 = ($outer_radius - $inner_radius) / 2;
        $r2 = $inner_radius + $r1;
        $rings = [];
        for ($i = 0; $i < $d1; ++$i) {
            $rings[$i] = [];
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
            foreach (array_keys($ring) as $j) {
                $j_next = ($j + 1) % count($ring);

                $this->addPolygon(new Polygon($rings[$i_next][$j], $rings[$i][$j], $rings[$i][$j_next]));
                $this->addPolygon(new Polygon($rings[$i_next][$j], $rings[$i][$j_next], $rings[$i_next][$j_next]));
            }
        }
    }
}
