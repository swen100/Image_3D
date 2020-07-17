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
class /*Image_3D_Object_*/Sphere extends \Image3D\Paintable\/*Image_3D_*/ Base3DObject
{

    /**
     * @var array
     */
    protected $_points = [];
    
    /**
     * @var array
     */
    protected $_virtualPolygon = [];
    
    /**
     * @var float
     */
    protected $_radius;

    /**
     *
     * @param array $options ['r' => .., 'detail' => ..]
     */
    public function __construct($options)
    {
        parent::__construct();

        $this->_radius = (float) $options['r'];
        $detail = (int) $options['detail'];

        $this->createTetraeder();

        for ($step = 0; $step < $detail; $step++) {
            $this->sierpinsky();
        }

        $this->getRealPolygones();
    }

    protected function sierpinsky()
    {
        $newPolygones = array();
        $proceededLines = array();
        foreach ($this->_virtualPolygon as $points) {
            $lines = array(
                array(min($points[0], $points[1]), max($points[0], $points[1])),
                array(min($points[1], $points[2]), max($points[1], $points[2])),
                array(min($points[2], $points[0]), max($points[2], $points[0]))
            );

            $new = array();
            foreach ($lines as $line) {
                if (!isset($proceededLines[$line[0]][$line[1]])) {
                    // Calculate new point
                    $newX = ($this->_points[$line[0]]->getX() + $this->_points[$line[1]]->getX()) / 2;
                    $newY = ($this->_points[$line[0]]->getY() + $this->_points[$line[1]]->getY()) / 2;
                    $newZ = ($this->_points[$line[0]]->getZ() + $this->_points[$line[1]]->getZ()) / 2;

                    $multiplikator = $this->_radius / sqrt(pow($newX, 2) + pow($newY, 2) + pow($newZ, 2));

                    $this->_points[] = new Point($newX * $multiplikator, $newY * $multiplikator, $newZ * $multiplikator);

                    $proceededLines[$line[0]][$line[1]] = count($this->_points) - 1;
                }
                $new[] = $proceededLines[$line[0]][$line[1]];
            }

            $newPolygones[] = array($points[0], $new[0], $new[2]);
            $newPolygones[] = array($points[1], $new[1], $new[0]);
            $newPolygones[] = array($points[2], $new[2], $new[1]);
            $newPolygones[] = array($new[0], $new[1], $new[2]);
        }
        $this->_virtualPolygon = $newPolygones;
    }

    protected function createTetraeder()
    {
        $laenge = $this->_radius / sqrt(3);

        $this->_points[] = new Point(sqrt(2) * -$laenge, $laenge, 0);
        $this->_points[] = new Point(sqrt(2) * $laenge, $laenge, 0);
        $this->_points[] = new Point(0, -$laenge, sqrt(2) * -$laenge);
        $this->_points[] = new Point(0, -$laenge, sqrt(2) * $laenge);

        $this->_virtualPolygon[] = array(0, 1, 3);
        $this->_virtualPolygon[] = array(1, 2, 3);
        $this->_virtualPolygon[] = array(0, 2, 1);
        $this->_virtualPolygon[] = array(0, 3, 2);
    }

    protected function getRealPolygones()
    {
        foreach ($this->_virtualPolygon as $points) {
            $this->addPolygon(new Polygon($this->_points[$points[0]], $this->_points[$points[1]], $this->_points[$points[2]]));
        }
    }
}
