<?php

namespace Image3D;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @license   http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_3D
 * @since     Class available since Release 0.1.0
 */
abstract class Driver
{

    /**
     * Worlds polygones
     *
     * @var mixed
     */
    protected $_image;

    /**
     * Initialize image
     *
     * Initialize the image with given width and height
     *
     * @param number $x Width
     * @param number $y Height
     *
     * @return void
     */
    abstract public function createImage($x, $y);

    /**
     * Sets Background
     *
     * Set the background for the image
     *
     * @param Color $color Backgroundcolor
     *
     * @return void
     */
    abstract public function setBackground(Color $color);

    /**
     * Draws a flat shaded polygon
     *
     * Draws a flat shaded polygon. Methd uses the polygon color
     *
     * @param Paintable\Polygon $polygon
     *
     * @return void
     */
    abstract public function drawPolygon(\Image3D\Paintable\Polygon $polygon);

    /**
     * Draws a gauroud shaded polygon
     *
     * Draws a gauroud shaded polygon. Methd uses the colors of the polygones
     * points and tries to create a gradient filling for the polygon.
     *
     * @param Paintable\Polygon $polygon
     *
     * @return void
     */
    abstract public function drawGradientPolygon(\Image3D\Paintable\Polygon $polygon);

    /**
     * Save image to file
     *
     * @param string $file File
     *
     * @return void
     */
    abstract public function save($file);

    /**
     * Return supported shadings
     *
     * Return an array with the shading types the driver supports
     *
     * @return array Array with supported shading types
     */
    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO
        ];
    }
}
