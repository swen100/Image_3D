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
class Map extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var array[[Point]]
     */
    protected $_points = [];

    /**
     * @param array $points
     */
    public function __construct(array $points = [])
    {
        foreach ($points as $row) {
            if (is_array($row)) {
                $this->addRow($row);
            } else {
                $this->addRow([$row]);
            }
        }
    }

    /**
     * @param array $row
     * @return bool
     */
    public function addRow(array $row = []): bool
    {
        if (empty($row)) {
            return false;
        }
        
        $rowNbr = array_push($this->_points, []) - 1;
        foreach ($row as $point) {
            if (is_object($point) && ($point instanceof Point)) {
                $this->_points[$rowNbr][] = $point;
            }
        }

        if (!array_key_exists($rowNbr, $this->_points) OR empty($this->_points[$rowNbr])) {
            unset($this->_points[$rowNbr]);
            return false;
        }

        if (count($this->_points) > 1) {
            $newRow = count($this->_points) - 1;
            $lastRow = $newRow - 1;

            $newCount = count($this->_points[$newRow]);
            $lastCount = count($this->_points[$lastRow]);

            if ($newCount < $lastCount) {
                $tmp = $newRow;
                $newRow = $lastRow;
                $lastRow = $tmp;

                $tmp = $newCount;
                $newCount = $lastCount;
                $lastCount = $tmp;
            }

            $top = (($newCount == 1) ? 1 : 1 / ($newCount - 1));
            $bottom = (($lastCount == 1) ? 32768 : 1 / ($lastCount - 1));

            $k = 0;
            for ($i = 1; $i < $newCount; $i++) {
                if (($i * $top) > ($k * $bottom + $bottom / 2)) {
                    // Nach unten geoeffnetes Polygon einfuegen
                    $this->addPolygon(new Polygon($this->_points[$newRow][$i - 1], $this->_points[$lastRow][$k + 1], $this->_points[$lastRow][$k]));
                    ++$k;
                }
                // Nach oben geoeffnetes Polygon einfuegen
                $this->addPolygon(new Polygon($this->_points[$newRow][$i - 1], $this->_points[$newRow][$i], $this->_points[$lastRow][$k]));
            }
        }
        
        return true;
    }

    /**
     * @param int $x
     * @return array|false
     */
    public function getRow(int $x)
    {
        if (!isset($this->_points[$x])) {
            return false;
        }
        return $this->_points[$x];
    }

    /**
     * @param int $x
     * @param int $y
     * @return Point|false
     */
    public function getPoint(int $x, int $y)
    {
        if (!isset($this->_points[$x][$y])) {
            return false;
        }
        return $this->_points[$x][$y];
    }

    /**
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value)
    {
//        if ($option === Image_3D::IMAGE_3D_OPTION_BF_CULLING) $value = false;
        parent::setOption($option, $value);
    }
}
