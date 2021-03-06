<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Point;
use Image3D\Paintable\Polygon;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ZBuffer extends \Image3D\Driver\GD
{

    /**
     * @var array
     */
    protected $_points = [];
    
    /**
     * @var array
     */
    protected $_heigth = [];

    /**
     * @param number $x width of the image
     * @param number $y height of the image
     */
    public function createImage($x, $y)
    {
        $this->_image = imagecreatetruecolor((int) $x, (int) $y);
        imagealphablending($this->_image, true);
        imageSaveAlpha($this->_image, true);
    }

    /**
     * @param Color $colorObj
     * @param float $alpha
     * @return int
     */
    protected function getColor(Color $colorObj, $alpha = 1.): int
    {
        $values = $colorObj->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        $values[3] = (int) round((1 - ((1 - $values[3]) * $alpha)) * 127);

        if ($values[3] > 0) {
            // Tranzparente Farbe allokieren
            $color = imageColorExactAlpha($this->_image, $values[0], $values[1], $values[2], $values[3]);
            if ($color === -1) {
                // Wenn nicht Farbe neu alloziieren
                $color = imageColorAllocateAlpha($this->_image, $values[0], $values[1], $values[2], $values[3]);
            }
        } else {
            // Deckende Farbe allozieren
            $color = imageColorExact($this->_image, $values[0], $values[1], $values[2]);
            if ($color === -1) {
                // Wenn nicht Farbe neu alloziieren
                $color = imageColorAllocate($this->_image, $values[0], $values[1], $values[2]);
            }
        }

        return $color;
    }

    /**
     * @param Point $p1
     * @param Point $p2
     * @return array
     */
    protected function drawLine(Point $p1, Point $p2): array
    {
        list($x1, $y1) = $p1->getScreenCoordinates();
        list($x2, $y2) = $p2->getScreenCoordinates();

        $z1 = $p1->getZ();
        $z2 = $p2->getZ();

        $steps = ceil(max(abs($x1 - $x2), abs($y1 - $y2)));

        $xdiff = ($x2 - $x1) / $steps;
        $ydiff = ($y2 - $y1) / $steps;
        $zdiff = ($z2 - $z1) / $steps;

        $points = ['height' => [], 'coverage' => []];
        for ($i = 0; $i < $steps; $i++) {
            $x = $x1 + $i * $xdiff;

            $xFloor = floor($x);
            $xCeil = ceil($x);
            $xOffset = $x - $xFloor;

            $y = $y1 + $i * $ydiff;

            $yFloor = floor($y);
            $yCeil = ceil($y);
            $yOffset = $y - $yFloor;

            if (!isset($points['coverage'][(int) $xFloor][(int) $yCeil])) {
                $points['height'][(int) $xFloor][(int) $yCeil] = $z1 + $i * $zdiff;
                $points['coverage'][(int) $xFloor][(int) $yCeil] = (1 - $xOffset) * $yOffset;
            } else {
                $points['coverage'][(int) $xFloor][(int) $yCeil] += (1 - $xOffset) * $yOffset;
            }

            if (!isset($points['coverage'][(int) $xFloor][(int) $yFloor])) {
                $points['height'][(int) $xFloor][(int) $yFloor] = $z1 + $i * $zdiff;
                $points['coverage'][(int) $xFloor][(int) $yFloor] = (1 - $xOffset) * (1 - $yOffset);
            } else {
                $points['coverage'][(int) $xFloor][(int) $yFloor] += (1 - $xOffset) * (1 - $yOffset);
            }

            if (!isset($points['coverage'][(int) $xCeil][(int) $yCeil])) {
                $points['height'][(int) $xCeil][(int) $yCeil] = $z1 + $i * $zdiff;
                $points['coverage'][(int) $xCeil][(int) $yCeil] = $xOffset * $yOffset;
            } else {
                $points['coverage'][(int) $xCeil][(int) $yCeil] += $xOffset * $yOffset;
            }

            if (!isset($points['coverage'][(int) $xCeil][(int) $yFloor])) {
                $points['height'][(int) $xCeil][(int) $yFloor] = $z1 + $i * $zdiff;
                $points['coverage'][(int) $xCeil][(int) $yFloor] = $xOffset * (1 - $yOffset);
            } else {
                $points['coverage'][(int) $xCeil][(int) $yFloor] += $xOffset * (1 - $yOffset);
            }
        }
        
        return $points;
    }

    /**
     * @param array $pointArray
     * @return array
     */
    protected function getPolygonOutlines(array $pointArray): array
    {
        $map = ['height' => [], 'coverage' => []];

        $last = end($pointArray);
        foreach ($pointArray as $point) {
            $line = $this->drawLine($last, $point);
            $last = $point;
            // Merge line to map
            foreach ($line['height'] as $x => $row) {
                foreach ($row as $y => $height) {
                    $map['height'][(int) $x][(int) $y] = $height;
                    $map['coverage'][(int) $x][(int) $y] = $line['coverage'][(int) $x][(int) $y];
                }
            }
        }

        return $map;
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawPolygon(Polygon $polygon)
    {
        $points = $this->getPolygonOutlines($polygon->getPoints());

        foreach ($points['coverage'] as $x => $row) {
            if (count($row) < 2) {
                continue;
            }

            $start = min(array_keys($row));
            $end = max(array_keys($row));

            $zStart = $points['height'][$x][$start];
            $zEnd = $points['height'][$x][$end];
            $zStep = ($zEnd - $zStart) / ($end - $start);

            // Starting point
            $this->_heigth[$x][$start][(int) ($zStart * 100)] = $this->getColor($polygon->getColor(), $points['coverage'][$x][$start]);

            // the way between
            for ($y = $start + 1; $y < $end; $y++) {
                $this->_heigth[$x][$y][(int) (($zStart + $zStep * ($y - $start)) * 100)] = $this->getColor($polygon->getColor());
            }

            // Ending point
            $this->_points[$x][$end][(int) ($zEnd * 100)] = $this->getColor($polygon->getColor(), $points['coverage'][$x][$end]);
        }
    }

    /**
     * @param string $filePath Path to the file where to write the data.
     * @return bool
     */
    public function save(string $filePath): bool
    {
        foreach ($this->_heigth as $x => $row) {
            foreach ($row as $y => $points) {
                krsort($points);
                foreach ($points as $color) {
                    imagesetpixel($this->_image, $x, $y, $color);
                }
            }
        }

        return parent::save($filePath);
    }
}
