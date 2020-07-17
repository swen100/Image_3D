<?php

namespace Image3D\Paintable\Object;

use Image3D\Point;

/**
 * Image_3D_Object_Bezier
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_3D
 * @since      Class available since Release 0.3.0
 */
class Bezier extends Map
{

    /**
     * 
     * @param array $options
     * @return void
     */
    public function __construct($options)
    {
        // Fetch options
        $x_detail = max(2, (int) $options['x_detail']);
        $y_detail = max(2, (int) $options['y_detail']);

        if (!isset($options['points']) || !is_array($options['points'])) {
            return;
        }

        $points = array();
        foreach ($options['points'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $points[] = array();
            $akt_row = count($points) - 1;

            foreach ($row as $point) {
                if (!is_array($point)) {
                    continue;
                }
                $points[$akt_row][] = $point;
            }
        }

        $n = count($points) - 1;
        $m = count($points[0]) - 1;
        $map = array();

        for ($u = 0; $u <= $x_detail; ++$u) {
            for ($v = 0; $v <= $y_detail; ++$v) {
                $point = array(0, 0, 0);

                for ($i = 0; $i <= $n; ++$i) {
                    for ($j = 0; $j <= $m; ++$j) {
                        $factor = $this->bernstein($i, $n, $u / $x_detail) * $this->bernstein($j, $m, $v / $y_detail);
                        $point[0] += $points[$i][$j][0] * $factor;
                        $point[1] += $points[$i][$j][1] * $factor;
                        $point[2] += $points[$i][$j][2] * $factor;
                    }
                }

                $map[$u][$v] = new Point($point[0], $point[1], $point[2]);
            }
        }

        parent::__construct($map);
    }

    /**
     * 
     * @param number $n
     * @param number $k
     * @return int
     */
    private function binomialCoefficient($n, $k)
    {
        if ($k > $n) {
            return 0;
        }
        if ($k == 0) {
            return 1;
        }

        if (2 * $k > $n) {
            $result = $this->binomialCoefficient($n, $n - $k);
        } else {
            $result = $n;
            for ($i = 2; $i <= $k; ++$i) {
                $result *= $n + 1 - $i;
                $result /= $i;
            }
        }

        return $result;
    }

    /**
     * 
     * @param number $i
     * @param number $n
     * @param number $t
     * @return number
     */
    private function bernstein($i, $n, $t)
    {
        return $this->binomialCoefficient($n, $i) * pow($t, $i) * pow(1 - $t, $n - $i);
    }
}
