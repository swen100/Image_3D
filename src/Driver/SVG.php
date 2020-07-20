<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * Class to create SVG.
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class SVG extends \Image3D\Driver
{

    /**
     * @var int Width of the image
     */
    protected $_x;

    /**
     * @var int Height of the image
     */
    protected $_y;

    /**
     * @var int Current, increasing element-id (integer)
     */
    protected $_id = 1;

    /**
     * @var array Array of gradients
     */
    protected $_gradients = [];

    /**
     * @var array Array of polygones
     */
    protected $_polygones = [];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_image = '';
    }

    /**
     * Creates image header
     *
     * @param number $x width of the image
     * @param number $y height of the image
     * @return void
     */
    public function createImage($x, $y)
    {
        $this->_x = (int) $x;
        $this->_y = (int) $y;

        $this->_image = <<<EOF
<?xml version="1.0" ?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
         "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="{$this->_x}" height="{$this->_y}">
EOF;

        $this->_image .= "\n\n";
    }

    /**
     * Adds coloured background to the image.
     *
     * Draws a rectangle with the size of the image and the passed colour.
     *
     * @param Color $color Background colour of the image
     * @return void
     */
    public function setBackground(Color $color)
    {
        $this->addPolygon(sprintf("\t<polygon id=\"background%d\" points=\"0,0 %d,0 %d,%d 0,%d\" style=\"%s\" />\n", $this->_id++, $this->_x, $this->_x, $this->_y, $this->_y, $this->getStyle($color)));
    }

    /**
     * @param Color $color
     * @return string
     */
    protected function getStyle(Color $color): string
    {
        $values = $color->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        $values[3] = 1 - $values[3];

        return sprintf('fill: #%02x%02x%02x; fill-opacity: %.2f; stroke: none;', $values[0], $values[1], $values[2], $values[3]);
    }

    /**
     * @param Color $color
     * @param number $offset default 0.0
     * @param number|null $alpha
     * @return string
     */
    protected function getStop(Color $color, $offset = 0.0, $alpha = null): string
    {
        $values = $color->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        
        if ($alpha === null) {
            $values[3] = 1 - $values[3];
        } else {
            $values[3] = 1 - $alpha;
        }

        return sprintf("\t\t\t<stop id=\"stop%d\" offset=\"%.1f\" style=\"stop-color:rgb(%d, %d, %d); stop-opacity:%.4f;\" />\n", $this->_id++, $offset, $values[0], $values[1], $values[2], $values[3]);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function addGradient(string $string): string
    {
        $id = 'linearGradient' . $this->_id++;

        $this->_gradients[] = str_replace('[id]', $id, $string);
        return $id;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function addPolygon(string $string): string
    {
        $id = 'polygon' . $this->_id++;

        $this->_polygones[] = str_replace('[id]', $id, $string);
        return $id;
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawPolygon(Polygon $polygon)
    {
        $list = '';
        $points = $polygon->getPoints();
        
        foreach ($points as $point) {
            $pointarray = $point->getScreenCoordinates();
            $list .= sprintf('%.2f,%.2f ', $pointarray[0], $pointarray[1]);
        }

        $this->addPolygon(
            sprintf(
                "\t<polygon points=\"%s\" style=\"%s\" />\n",
                substr($list, 0, -1),
                $this->getStyle($polygon->getColor())
            )
        );
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawGradientPolygon(Polygon $polygon)
    {
        $points = $polygon->getPoints();

        $list = '';

        $pointarray = array();
        foreach ($points as $nr => $point) {
            $pointarray[$nr] = $point->getScreenCoordinates();

            $list .= sprintf('%.2f,%.2f ', $pointarray[$nr][0], $pointarray[$nr][1]);
        }

        // Groessen
        $xOffset = min($pointarray[0][0], $pointarray[1][0], $pointarray[2][0]);
        $yOffset = min($pointarray[0][1], $pointarray[1][1], $pointarray[2][1]);

        $xSize = max(abs($pointarray[0][0] - $pointarray[1][0]), abs($pointarray[0][0] - $pointarray[2][0]), abs($pointarray[1][0] - $pointarray[2][0]));
        $ySize = max(abs($pointarray[0][1] - $pointarray[1][1]), abs($pointarray[0][1] - $pointarray[2][1]), abs($pointarray[1][1] - $pointarray[2][1]));

        // Base Polygon
        $lg = $this->addGradient(sprintf("\t\t<linearGradient id=\"[id]\" x1=\"%.2f\" y1=\"%.2f\" x2=\"%.2f\" y2=\"%.2f\">\n%s\t\t</linearGradient>\n", ($pointarray[0][0] - $xOffset) / $xSize, ($pointarray[0][1] - $yOffset) / $ySize, ($pointarray[1][0] - $xOffset) / $xSize, ($pointarray[1][1] - $yOffset) / $ySize, $this->getStop($points[0]->getColor()) . $this->getStop($points[1]->getColor(), 1)));

        $this->addPolygon(sprintf("\t<polygon id=\"[id]\" points=\"%s\" style=\"fill: url(#%s); stroke: none; fill-opacity: 1;\" />\n", $list, $lg));

        // Overlay Polygon
        $lg = $this->addGradient(sprintf("\t\t<linearGradient id=\"[id]\" x1=\"%.2f\" y1=\"%.2f\" x2=\"%.2f\" y2=\"%.2f\">\n%s\t\t</linearGradient>\n", ($pointarray[2][0] - $xOffset) / $xSize, ($pointarray[2][1] - $yOffset) / $ySize, ((($pointarray[0][0] + $pointarray[1][0]) / 2) - $xOffset) / $xSize, ((($pointarray[0][1] + $pointarray[1][1]) / 2) - $yOffset) / $ySize, $this->getStop($points[2]->getColor()) . $this->getStop($points[2]->getColor(), 1, 1)));

        $this->addPolygon(sprintf("\t<polygon id=\"[id]\" points=\"%s\" style=\"fill: url(#%s); stroke: none; fill-opacity: 1;\" />\n", $list, $lg));
    }

    /**
     * @param string $filePath Path to the file where to write the data.
     * @return bool
     */
    public function save(string $filePath): bool
    {
        $this->_image .= sprintf("\t<defs id=\"defs%d\">\n", $this->_id++);
        $this->_image .= implode('', $this->_gradients);
        $this->_image .= sprintf("\t</defs>\n\n");

        $this->_image .= implode('', $this->_polygones);
        $this->_image .= "</svg>\n";
        
        return file_put_contents($filePath, $this->_image) !== false;
    }

    /**
     * @return array
     */
    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO,
            Renderer::SHADE_FLAT,
            Renderer::SHADE_GAUROUD
        ];
    }
}
