<?php

namespace Image3D\Paintable;

use Image3D\Vector;
use Image3D\Color;
use Image3D\Point;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Polygon implements \Image3D\Paintable, \Image3D\Enlightenable
{

    /**
     * @var \Image3D\Color
     */
    protected $_color;
    
    /**
     * @var bool
     */
    protected $_colorCalculated = false;
    
    /**
     * @var array
     */
    protected $_option = [];
    
    /**
     * @var array
     */
    protected $_points = [];
    
    /**
     * @var bool
     */
    protected $_visible = true;
    
    /**
     * @var Vector
     */
    protected $_normale;
    
    /**
     * @var Vector
     */
    protected $_position;
    
    /**
     * @var array
     */
    protected $_boundingRect = [null, null, null, null, null, null];

    /**
     *
     */
    public function __construct()
    {
        if (func_num_args()) {
            $args = func_get_args();
            for ($i = 0; $i < func_num_args(); $i++) {
                if (is_object($args[$i]) && ($args[$i] instanceof Point)) {
                    $this->addPoint($args[$i]);
                }
            }
        }
    }

    /**
     * @param array $lights
     */
    public function calculateColor(array $lights = [])
    {
        foreach ($lights as $light) {
            $this->_color = $light->getColor($this);
        }
        $this->_color->calculateColor();
    }

    /**
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->_color ?? new Color(255, 255, 255, 0);
    }

    /**
     * @return bool
     */
    protected function calcNormale(): bool
    {
        if (count($this->_points) < 3) {
            $this->_normale = new Vector(0, 0, 0);
            return false;
        }

        $a1 = $this->_points[0]->getX() - $this->_points[1]->getX();
        $a2 = $this->_points[0]->getY() - $this->_points[1]->getY();
        $a3 = $this->_points[0]->getZ() - $this->_points[1]->getZ();
        $b1 = $this->_points[2]->getX() - $this->_points[1]->getX();
        $b2 = $this->_points[2]->getY() - $this->_points[1]->getY();
        $b3 = $this->_points[2]->getZ() - $this->_points[1]->getZ();

        $this->_normale = new Vector($a2 * $b3 - $a3 * $b2, $a3 * $b1 - $a1 * $b3, $a1 * $b2 - $a2 * $b1);

        // Backface Culling
        //if( ($this->_normale->getZ() <= 0) && ($this->_option[\Image3D\Image_3D::IMAGE_3D_OPTION_BF_CULLING]) ) {
        if (($this->_normale->getZ() <= 0) && isset($this->_option[\Image3D\Image_3D::IMAGE_3D_OPTION_BF_CULLING])) {
            $this->setInvisible();
        }
        
        return true;
    }

    /**
     * @return Vector
     */
    public function getNormale(): Vector
    {
        if (!($this->_normale instanceof Vector)) {
            $this->calcNormale();
        }
        return $this->_normale;
    }

    /**
     * @return void
     */
    protected function calcPosition()
    {
        $position = [0, 0, 0];
        foreach ($this->_points as $point) {
            $position[0] += $point->getX();
            $position[1] += $point->getY();
            $position[2] += $point->getZ();
        }
        $count = count($this->_points);

        $this->_position = new Vector($position[0] / $count, $position[1] / $count, $position[2] / $count);
    }

    /**
     * @return Vector
     */
    public function getPosition(): Vector
    {
        if (!($this->_position instanceof Vector)) {
            $this->calcPosition();
        }
        return $this->_position;
    }

    /**
     * @return int 1
     */
    public function getPolygonCount(): int
    {
        return 1;
    }

    /**
     * @param Color $color
     * @return void
     */
    public function setColor(Color $color)
    {
        $this->_color = $color;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->_visible;
    }

    /**
     * @return void
     */
    public function setInvisible()
    {
        $this->_visible = false;
    }
    
    /**
     * @param bool $visibility default false
     * @return void
     */
    public function setVisibility(bool $visibility = false)
    {
        $this->_visible = $visibility;
    }

    /**
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value)
    {
        $this->_option[$option] = $value;
        foreach ($this->_points as $point) {
            $point->setOption($option, $value);
        }
    }

    /**
     * @param Point $point
     * @return void
     */
    public function addPoint(Point $point)
    {
        $this->_points[] = $point;

        // Adjust bounding rectangle
        if (!isset($this->_boundingRect[0]) || ($point->getX() < $this->_boundingRect[0])) {
            $this->_boundingRect[0] = $point->getX();
        }

        if (!isset($this->_boundingRect[1]) || ($point->getY() < $this->_boundingRect[1])) {
            $this->_boundingRect[1] = $point->getY();
        }

        if (!isset($this->_boundingRect[2]) || ($point->getZ() < $this->_boundingRect[2])) {
            $this->_boundingRect[2] = $point->getZ();
        }

        if (!isset($this->_boundingRect[3]) || ($point->getX() > $this->_boundingRect[3])) {
            $this->_boundingRect[3] = $point->getX();
        }

        if (!isset($this->_boundingRect[4]) || ($point->getY() > $this->_boundingRect[4])) {
            $this->_boundingRect[4] = $point->getY();
        }

        if (!isset($this->_boundingRect[5]) || ($point->getZ() > $this->_boundingRect[5])) {
            $this->_boundingRect[5] = $point->getZ();
        }
    }

    /**
     * @return array
     */
    public function getPoints(): array
    {
        return $this->_points;
    }

    /**
     * @return void
     */
    protected function recalcBoundings()
    {
        $this->_boundingRect = [null, null, null, null, null, null];

        foreach ($this->_points as $point) {
            if (!isset($this->_boundingRect[0]) || ($point->getX() < $this->_boundingRect[0])) {
                $this->_boundingRect[0] = $point->getX();
            }

            if (!isset($this->_boundingRect[1]) || ($point->getY() < $this->_boundingRect[1])) {
                $this->_boundingRect[1] = $point->getY();
            }

            if (!isset($this->_boundingRect[2]) || ($point->getZ() < $this->_boundingRect[2])) {
                $this->_boundingRect[2] = $point->getZ();
            }

            if (!isset($this->_boundingRect[3]) || ($point->getX() > $this->_boundingRect[3])) {
                $this->_boundingRect[3] = $point->getX();
            }

            if (!isset($this->_boundingRect[4]) || ($point->getY() > $this->_boundingRect[4])) {
                $this->_boundingRect[4] = $point->getY();
            }

            if (!isset($this->_boundingRect[5]) || ($point->getZ() > $this->_boundingRect[5])) {
                $this->_boundingRect[5] = $point->getZ();
            }
        }
    }

    /**
     * @param \Image3D\Matrix $matrix
     * @param string $id
     * @return void
     */
    public function transform(\Image3D\Matrix $matrix, $id = null)
    {
        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }

        foreach ($this->_points as $point) {
            $point->transform($matrix, $id);
        }

        $this->recalcBoundings();
    }

    /**
     * @return float
     */
    public function getMidZ(): float
    {
        $z = 0;
        foreach ($this->_points as $point) {
            $z += $point->getZ();
        }
        return (float) ($z / count($this->_points));
    }

    /**
     * @return float
     */
    public function getMaxZ(): float
    {
        $z = PHP_INT_MIN;
        foreach ($this->_points as $point) {
            $z = max($point->getZ(), $z);
        }
        return (float) $z;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = "Polygon:\n";
        foreach ($this->_points as $point) {
            $string .= "\t" . $point->__toString() . "\n";
        }
        return $string;
    }

    /**
     * @param \Image3D\Line $line
     * @return false|float
     */
    public function distance(\Image3D\Line $line)
    {
        // Calculate parameters for plane
        $normale = $this->getNormale();

        $A = $normale->getX();
        $B = $normale->getY();
        $C = $normale->getZ();

        $D = -($normale->getX() * $this->_points[0]->getX() +
                $normale->getY() * $this->_points[0]->getY() +
                $normale->getZ() * $this->_points[0]->getZ());

        // Calculate wheather and where line cuts the polygons plane
        $lineDirection = $line->getDirection();

        $denominator = -( $A * $line->getX() +
                $B * $line->getY() +
                $C * $line->getZ() +
                $D);

        $numerator = ( $A * $lineDirection->getX() +
                $B * $lineDirection->getY() +
                $C * $lineDirection->getZ());

        // Nu cut, when denomintor equals 0 (parallel plane)
        if ((int) ($denominator * 100000) === 0) {
            return false;
        }

        if ((int) ($numerator * 100000) === 0) {
            return false;
        }

        $t = $denominator / $numerator;

        // No cut, when $t < 0 (plane is behind the camera)
        if ($t <= 0) {
            return false;
        }

        // TODO: Perhaps add max distance check with unified normale vector
        // Calculate cutting point between line an plane
        $cuttingPoint = $line->calcPoint($t);

        // Perform fast check for point in bounding cube;
        if (($cuttingPoint->getX() < $this->_boundingRect[0]) ||
                ($cuttingPoint->getY() < $this->_boundingRect[1]) ||
                ($cuttingPoint->getZ() < $this->_boundingRect[2]) ||
                ($cuttingPoint->getX() > $this->_boundingRect[3]) ||
                ($cuttingPoint->getY() > $this->_boundingRect[4]) ||
                ($cuttingPoint->getZ() > $this->_boundingRect[5])) {
            return false;
        }

        // perform exact check for point in polygon
        $lastScalar = 0;
        foreach ($this->_points as $nr => $point) {
            $nextPoint = $this->_points[($nr + 1) % count($this->_points)];

            $edge = new Vector($nextPoint->getX() - $point->getX(), $nextPoint->getY() - $point->getY(), $nextPoint->getZ() - $point->getZ());

            $v = new Vector($cuttingPoint->getX() - $point->getX(), $cuttingPoint->getY() - $point->getY(), $cuttingPoint->getZ() - $point->getZ());

            $scalar = $edge->crossProduct($v)->scalar($normale);

            if ($scalar * $lastScalar >= 0) {
                $lastScalar = $scalar;
            } else {
                return false;
            }
        }

        // Point is in polygon, return distance to polygon
        return (float) $t;
    }
}
