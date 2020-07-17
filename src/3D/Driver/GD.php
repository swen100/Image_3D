<?php
namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * Class for raster-output.
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @license   http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_3D
 * @since     Class available since Release 0.1.0
 */
class GD extends \Image3D\Driver
{
    /**
     * @var string
     */
    protected $_filetype = 'png';

    /**
     * @param number $x
     * @param number $y
     */
    public function createImage($x, $y)
    {
        $this->_image = imagecreatetruecolor($x, $y);
    }

    /**
     *
     * @param Color $colorObj
     * @return int
     */
    protected function getColor(Color $colorObj): int
    {
        $values = $colorObj->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        $values[3] = (int) round($values[3] * 127);

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
     *
     * @param Color $color
     */
    public function setBackground(Color $color)
    {
        $bg = $this->getColor($color);
        imagefill($this->_image, 1, 1, $bg);
    }

    /**
     *
     * @param Polygon $polygon
     */
    public function drawPolygon(Polygon $polygon)
    {
        // Get points
        $points = $polygon->getPoints();
        $coords = array();
        foreach ($points as $point) {
            $coords = array_merge($coords, $point->getScreenCoordinates());
        }
        $coordCount = (int) (count($coords) / 2);

        #if( true ) {
            imageFilledPolygon($this->_image, $coords, $coordCount, $this->getColor($polygon->getColor()));
        #} else {
        #    imagePolygon($this->_image, $coords, $coordCount, $this->getColor($polygon->getColor()));
        #}
    }

    /**
     *
     * @param Polygon $polygon
     */
    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    /**
     *
     * @param string $type
     * @return bool
     */
    public function setFiletye($type): bool
    {
        $type = strtolower($type);
        if (\in_array($type, array('png', 'jpeg'))) {
            $this->_filetype = $type;
            return true;
        }
        
        return false;
    }

    /**
     *
     * @param string $filePath
     * @return bool
     */
    public function save($filePath): bool
    {
        switch ($this->_filetype) {
            case 'png':
                return imagepng($this->_image, $filePath);
            case 'jpeg':
                return imagejpeg($this->_image, $filePath);
        }
    }

    /**
     *
     * @return array
     */
    public function getSupportedShading(): array
    {
        return [Renderer::SHADE_NO, Renderer::SHADE_FLAT];
    }
}
