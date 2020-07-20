<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ImageCanvas extends \Image3D\Driver
{

    /**
     * @var string
     */
    protected $_type;

    /**
     * @param string $type
     * @return void
     */
    public function setImageType(string $type)
    {
        $this->_type = $type;
    }

    /**
     * @param number$x
     * @param number $y
     * @return void
     */
    public function createImage($x, $y)
    {
        // needs https://github.com/csatf/Image_Canvas
        /* @phpstan-ignore-next-line */
        $this->_image = Image_Canvas::factory(
            $this->_type,
            ['width' => (int) $x, 'height' => (int) $y, 'antialias' => 'driver']
        );
    }

    /**
     * @param Color $color
     * @return string
     */
    protected function getColor(Color $color): string
    {
        $values = $color->getValues();
        return sprintf('#%02x%02x%02x@%f', (int) ($values[0] * 255), (int) ($values[1] * 255), (int) ($values[2] * 255), 1 - $values[3]);
    }

    /**
     * @param Color $color
     * @return void
     */
    public function setBackground(Color $color)
    {
        $this->_image->setFillColor($this->getColor($color));
        $this->_image->rectangle(array('x0' => 0, 'y0' => 0, 'x1' => $this->_image->getWidth(), 'y1' => $this->_image->getHeight()));
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawPolygon(Polygon $polygon)
    {
        // Build pointarray
        #$pointArray = array();
        $points = $polygon->getPoints();
        foreach ($points as $point) {
            $screenCoordinates = $point->getScreenCoordinates();
            $this->_image->addVertex(array('x' => $screenCoordinates[0], 'y' => $screenCoordinates[1]));
        }
        $this->_image->setFillColor($this->getColor($polygon->getColor()));
        $this->_image->setLineColor(false);
        $this->_image->polygon(array('connect' => true));
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    /**
     * @param string $filePath Path to the file where to write the data.
     * @return bool
     */
    public function save(string $filePath): bool
    {
        return $this->_image->save(['filename' => $filePath]) !== false;
    }

    /**
     * @return array
     */
    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO,
            Renderer::SHADE_FLAT
        ];
    }
}
