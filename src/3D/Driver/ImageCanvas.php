<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

class ImageCanvas extends \Image3D\Driver
{

    protected $_filetype;
    protected $_type;

    public function __construct()
    {
    }

    public function setImageType($type)
    {
        $this->_type = (string) $type;
    }

    public function createImage($x, $y)
    {
        // needs https://github.com/csatf/Image_Canvas
        /* @phpstan-ignore-next-line */
        $this->_image = Image_Canvas::factory($this->_type, ['width' => $x, 'height' => $y, 'antialias' => 'driver']);
    }

    protected function _getColor(Color $color)
    {
        $values = $color->getValues();
        return sprintf('#%02x%02x%02x@%f', (int) ($values[0] * 255), (int) ($values[1] * 255), (int) ($values[2] * 255), 1 - $values[3]);
    }

    public function setBackground(Color $color)
    {
        $this->_image->setFillColor($this->_getColor($color));
        $this->_image->rectangle(array('x0' => 0, 'y0' => 0, 'x1' => $this->_image->getWidth(), 'y1' => $this->_image->getHeight()));
    }

    public function drawPolygon(Polygon $polygon)
    {
        // Build pointarray
        #$pointArray = array();
        $points = $polygon->getPoints();
        foreach ($points as $point) {
            $screenCoordinates = $point->getScreenCoordinates();
            $this->_image->addVertex(array('x' => $screenCoordinates[0], 'y' => $screenCoordinates[1]));
        }
        $this->_image->setFillColor($this->_getColor($polygon->getColor()));
        $this->_image->setLineColor(false);
        $this->_image->polygon(array('connect' => true));
    }

    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    public function save($file): bool
    {
        return $this->_image->save(['filename' => $file]) !== false;
    }

    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO,
            Renderer::SHADE_FLAT
        ];
    }
}
