<?php

namespace Image3D;

use Image3D\Paintable\Light;

/**
 * Image_3D
 *
 * Class for creation of 3d images only with native PHP.
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
class Image_3D
{

    /**
     * Backgroundcolor
     *
     * @var Color
     */
    protected $_color;

    /**
     * List of known objects
     *
     * @var array
     */
    protected $_objects = [];

    /**
     * List of lights
     *
     * @var array
     */
    protected $_lights = [];

    /**
     * Active renderer
     *
     * @var Renderer
     */
    protected $_renderer;

    /**
     * Active outputdriver
     *
     * @var Driver
     */
    protected $_driver;

    /**
     * Options for rendering
     */
    protected $_option;

    /**
     * Options set by the user
     *
     * @var array
     */
    protected $_optionSet = [];

    /**
     * Begin of world creation
     *
     * @var float
     */
    protected $_start;

    /**
     * Option for filled polygones (depreceated)
     */
    const IMAGE_3D_OPTION_FILLED = 1;

    /**
     * Option for backface culling (depreceated)
     */
    const IMAGE_3D_OPTION_BF_CULLING = 2;

    /**
     * Constructor for Image_3D
     *
     * Initialises the environment
     *
     * @return void
     */
    public function __construct()
    {
        $this->_option[self::IMAGE_3D_OPTION_FILLED] = true;
        $this->_option[self::IMAGE_3D_OPTION_BF_CULLING] = true;
        
        $this->_start = microtime(true);
    }

    /**
     * Factory method for Objects
     *
     * Creates and returns a printable object.
     * Standard objects with parameters:
     *     - cube        array(float $x, float $y, float $z)
     *     - sphere    array(float $r, int $detail)
     *     - 3ds        string $filename
     *     - map        [array(array(Image_3D_Point))]
     *     - text        string $string
     *
     * @param string $type      Objectname
     * @param array  $parameter Parameters
     *
     * @return Paintable\Base3DObject Object instance
     */
    public function createObject($type, $parameter = [])
    {
        $name = ucfirst($type);
        $class = '\\Image3D\\Paintable\\Object\\' . $name;

        return $this->_objects[] = new $class($parameter);
    }

    /**
     * Factory method for lights
     *
     * Creates and returns a light. Needs only the position of the lights as a
     * parameter.
     *
     * @param string $type      Class
     * @param array  $parameter Parameters
     *
     * @return Light Object instance
     */
    public function createLight(string $type, array $parameter = [])
    {
        $name = ucfirst($type);
        if ($name != 'Light') {
            $class = '\\Image3D\\Paintable\\Light\\' . $name;
            return $this->_lights[] = new $class($parameter[0], $parameter[1], $parameter[2], array_slice($parameter, 3));
        } else {
            return $this->_lights[] = new Light($parameter[0], $parameter[1], $parameter[2]);
        }
    }

    /**
     * Factory method for transformation matrixes
     *
     * Creates a transformation matrix
     * Known matrix types:
     *  - rotation      array(float $x, float $y, float $z)
     *  - scale         array(float $x, float $y, float $z)
     *  - move          array(float $x, float $y, float $z)
     *
     * @param string $type      Matrix type
     * @param array  $parameter Parameters
     *
     * @return Matrix         Object instance
     */
    public function createMatrix(string $type, array $parameter = [])
    {
        $name = ucfirst($type);
        $class = '\\Image3D\\Matrix\\' . $name;

        return new $class($parameter);
    }

    /**
     * Sets world backgroundcolor
     *
     * Sets the backgroundcolor for final image. Transparancy is not supported
     * by all drivers
     *
     * @param Color $colorObj Backgroundcolor
     *
     * @return void
     */
    public function setColor(Color $colorObj)
    {
        $this->_color = $colorObj;
    }

    /**
     * Factory method for renderer
     *
     * Creates and returns a renderer.
     * Avaible renderers
     *  - Isometric
     *  - Perspektively
     *
     * @param string $type Renderer type
     *
     * @return Renderer Object instance
     */
    public function createRenderer(string $type)
    {
        $name = ucfirst($type);
        $class = '\\Image3D\\Renderer\\' . $name;

        return $this->_renderer = new $class();
    }

    /**
     * Factory method for drivers
     *
     * Creates and returns a new driver
     * Standrad available drivers:
     *  - GD
     *  - SVG
     *
     * @param string $type Driver type
     *
     * @return Driver Object instance
     */
    public function createDriver(string $type)
    {
        $name = ucfirst($type);
        $class = '\\Image3D\\Driver\\' . $name;

        return $this->_driver = new $class();
    }

    /**
     * Sets an option for all known objects
     *
     * Sets one of the Image_3D options for all known objects
     *
     * @param integer $option Option
     * @param mixed   $value  Value
     *
     * @return void
     */
    public function setOption($option, $value)
    {
        $this->_option[$option] = $value;
        $this->_optionSet[$option] = true;

        foreach ($this->_objects as $object) {
            $object->setOption($option, $value);
        }
    }

    /**
     * Transform all known objects
     *
     * Transform all known objects with the given transformation matrix.
     * Can be interpreted as a transformation of the viewpoint.
     *
     * The id is an optional value which shouldn't be set by the user to
     * avoid double calculations, if a point is related to more than one
     * object.
     *
     * @param Matrix $matrix  Transformation matrix
     * @param string|null $id Transformation ID
     *
     * @return void
     */
    public function transform(Matrix $matrix, $id = null)
    {
        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }

        foreach ($this->_objects as $object) {
            $object->transform($matrix, $id);
        }
    }

    /**
     * Renders the image
     *
     * Starts rendering an image with given size into the given file.
     *
     * @param integer $x    Width
     * @param integer $y    Height
     * @param string  $file Filename
     *
     * @return bool Success
     */
    public function render($x, $y, $file)
    {
        // Hack because stdout is not writeable
        if ((is_file($file) || !is_writeable(dirname($file))) &&
                (!is_file($file) || !is_writeable($file)) && !preg_match('/^\s*php:\/\/(stdout|output)\s*$/i', $file)) {
            throw new \Exception('Cannot write outputfile.');
        }

        $x = min(1280, max(0, (int) $x));
        $y = min(1280, max(0, (int) $y));

        $this->_renderer->setSize($x, $y);
        $this->_renderer->setBackgroundColor($this->_color);
        $this->_renderer->addObjects($this->_objects);
        $this->_renderer->addLights($this->_lights);
        $this->_renderer->setDriver($this->_driver);

        return $this->_renderer->render($file);
    }

    /**
     * Statistics for Image_3D
     *
     * Returns simple statisics for Image_3D as a string.
     *
     * @return string Statistics
     */
    public function stats()
    {
        return sprintf(
            'Image 3D
            
            objects:    %d
            lights:     %d
            polygones:  %d
            points:     %d

            time:       %.4f s
            ',
            count($this->_objects),
            $this->_renderer->getLightCount(),
            $this->_renderer->getPolygonCount(),
            $this->_renderer->getPointCount(),
            microtime(true) - $this->_start
        );
    }
}
