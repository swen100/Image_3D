<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * Creates a HTML document, with embedded javascript code to draw, move, rotate
 * and export the 3D-object at runtime
 *
 * @category Image
 * @package  Image_3D
 * @author   Jakob Westhoff <jakob@westhoffswelt.de>
 */
class DynamicCanvas extends \Image3D\Driver
{

    /**
     * Width of the image
     *
     * @var integer
     */
    protected $_x;

    /**
     * Height of the image
     *
     * @var integer
     */
    protected $_y;

    /**
     * Polygones created during the rendering process
     *
     * @var array
     */
    protected $_polygones = [];

    /**
     * Background Color of the rendered image
     *
     * @var string
     */
    protected $_background = [];

    /**
     * Name of the Render created from the filename
     * Needed for the correct creation of the Image3D java class
     *
     * @var mixed
     */
    protected $_name;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_image = '';
    }

    /**
     * Create the inital image
     *
     * @param number $x Width of the image
     * @param number $y Height of the image
     *
     * @return void
     */
    public function createImage($x, $y)
    {
        $this->_x = (int) $x;
        $this->_y = (int) $y;
    }

    /**
     * Set the background color of the image
     *
     * @param Color $color Desired background color of the image
     *
     * @return void
     */
    public function setBackground(Color $color)
    {
        $colorarray = $this->getRgba($color);

        $this->_background = sprintf(
            "{ r: %d, g: %d, b: %d, a:%.2f }",
            $colorarray['r'],
            $colorarray['g'],
            $colorarray['b'],
            $colorarray['a']
        );
    }

    /**
     * Create an appropriate array representation from a Image_3D_Color object
     *
     * @param Color $color Color to transform to rgba syntax
     * @param float          $alpha optional Override the alpha value set in the Image_3D_Color object
     *
     * @return array Array of color values reflecting the different color
     *               components of the input object
     */
    protected function getRgba(Color $color, $alpha = null)
    {
        $values = $color->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);

        if ($alpha !== null) {
            $values[3] = 1.0 - $alpha;
        } else {
            $values[3] = 1.0 - $values[3];
        }

        return array('r' => $values[0], 'g' => $values[1], 'b' => $values[2], 'a' => $values[3]);
    }

    /**
     * Add a polygon to the polygones array
     *
     * @param array $points Array of points which represent the polygon to add
     * @param array $colors Array of maximal three colors. The second and the
     *                      third color are allowed to be null
     *
     * @return void
     */
    protected function addPolygon(array $points, array $colors)
    {
        $this->_polygones[] = ["points" => $points, "colors" => $colors];
    }

    /**
     * Draw a specified polygon
     *
     * @param Polygon $polygon Polygon to draw
     *
     * @return void
     */
    public function drawPolygon(Polygon $polygon)
    {
        $pointarray = [];

        $points = $polygon->getPoints();
        foreach ($points as $key => $point) {
            $pointarray[$key] = ['x' => $point->getX(), 'y' => $point->getY(), 'z' => $point->getZ()];
        }

        $this->addPolygon($pointarray, [$this->getRgba($polygon->getColor()),
            null,
            null
        ]);
    }

    /**
     * Draw a specified polygon utilizing gradients between his points for
     * color representation (Gauroud-Shading)
     *
     * @param Polygon $polygon Polygon to draw
     *
     * @return void
     */
    public function drawGradientPolygon(Polygon $polygon)
    {
        $pointarray = [];
        $colorarray = [];

        $points = $polygon->getPoints();
        foreach ($points as $key => $point) {
            $pointarray[$key] = ['x' => $point->getX(), 'y' => $point->getY(), 'z' => $point->getZ()];
            $colorarray[$key] = $this->getRgba($point->getColor());
        }

        $this->addPolygon($pointarray, $colorarray);
    }

    /**
     * Convert php array to a javascript parsable data structure
     *
     * @param array $data Array to convert
     *
     * @return string Javascript readable representation of the given php array
     */
    private function arrayToJs(array $data)
    {
        $output = [];

        $assoiative = false;
        // Is our array associative?
        // Does anyone know a better/faster way to check this?
        foreach (array_keys($data) as $key) {
            if (is_int($key) === false) {
                $assoiative = true;
                break;
            }
        }
        $output[] = $assoiative === true ? "{" : "[";
        foreach ($data as $key => $value) {
            $line = '';

            if ($assoiative === true) {
                $line .= "\"$key\": ";
            }

            switch (gettype($value)) {
                case "array":
                    $line .= $this->arrayToJs($value);
                    break;
                case "integer":
                case "boolean":
                    $line .= $value;
                    break;
                case "double":
                    $line .= sprintf("%.2f", $value);
                    break;
                case "string":
                    $line .= "\"$value\"";
                    break;
                case "NULL":
                case "resource":
                case "object":
                    $line .= "undefined";
                    break;
            }

            $keys = array_keys($data);
            if ($key !== end($keys)) {
                $line .= ",";
            }
            $output[] = $line;
        }

        $output[] = $assoiative === true ? "}" : "]";

        // If the output array has more than 5 entries seperate them by a new line.
        return implode(count($data) > 5 ? "\n" : " ", $output);
    }

    /**
     * Get the Javascript needed for dynamic rendering, moving, rotating
     * and exporting of the 3D Object
     *
     * @return string needed javascript code (with <script> tags)
     */
    private function getJs()
    {
        $identifiers = [
            "%polygones%",
            "%background%",
            "%width%",
            "%height%",
            "%uid%"
        ];

        $replacements = [
            $this->arrayToJs($this->_polygones) . ";\n",
            $this->_background,
            $this->_x,
            $this->_y,
            sha1(mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand())
        ];

        $jsfiles = [
            'Init.js',
            'Renderer.js',
            'CanvasDriver.js',
            'PngDriver.js',
            'SvgDriver.js',
            'MouseEventGenerator.js',
            'RotateAnimationEventGenerator.js',
            'Toolbar.js',
            'Base64.js',
            'Image3D.js',
            'Startup.js'
        ];
        
        $loadJsFile = function($jsfile) {
            $dataDir = dirname(__FILE__) . '/../../../test/data/DynamicCanvas/';
            return is_dir($dataDir) ?
                file_get_contents($dataDir . $jsfile) :
                file_get_contents("@data_dir@/Image_3D/data/DynamicCanvas/" . $jsfile);
        };
        
        return str_replace(
            $identifiers,
            $replacements,
            implode("\n\n", array_map(
                $loadJsFile,
                $jsfiles
            ))
        );
    }

    /**
     * Save all the gathered information to a html file
     *
     * @param string $file File to write output to
     *
     * @return void
     */
    public function save($file)
    {
        file_put_contents($file, $this->getJs());
    }

    /**
     * Return the shading methods this output driver is capable of
     *
     * @return array Shading methods supported by this driver
     */
    public function getSupportedShading()
    {
        return array(Renderer::SHADE_NO, Renderer::SHADE_FLAT);
    }
}
