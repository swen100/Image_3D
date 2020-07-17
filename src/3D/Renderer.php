<?php

namespace Image3D;

use Image3D\Paintable\Base3DObject;

/**
 * Image_3D_Renderer
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
abstract class Renderer
{

    /**
     * Worlds polygones
     *
     * @var array
     */
    protected $_polygones;

    /**
     * Worlds points
     *
     * @var array
     */
    protected $_points;

    /**
     * Worlds lights
     *
     * @var array
     */
    protected $_lights;

    /**
     * Driver we use
     *
     * @var array
     */
    protected $_driver;

    /**
     * Size of the Image
     *
     * @var array
     */
    protected $_size;

    /**
     * Backgroundcolol
     *
     * @var Color
     */
    protected $_background;

    /**
     * Type of Shading used
     *
     * @var integer
     */
    protected $_shading;

    /*
     * No Shading
     */

    const SHADE_NO = 0;

    /*
     * Flat Shading
     */
    const SHADE_FLAT = 1;
    /*
     * Gauroud Shading
     */
    const SHADE_GAUROUD = 2;
    /*
     * Phong Shading
     */
    const SHADE_PHONG = 3;

    /**
     * Constructor for Image_3D_Renderer
     *
     * Initialises the environment
     *
     * @return  Renderer           Instance of Renderer
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Reset all changeable variables
     *
     * Initialises the environment
     *
     * @return  void
     */
    public function reset()
    {
        $this->_objects = array();
        $this->_polygones = array();

        $this->_points = array();
        $this->_lights = array();
        $this->_size = array(0, 0);

        $this->_background = null;

        $this->_driver = null;

        $this->_shading = self::SHADE_PHONG;
    }

    /**
     * Get and merge polygones
     *
     * Get polygones and points from an object and merge them unique to local
     * polygon- and pointarrays.
     *
     * @param Base3DObject $object Object to merge
     *
     * @return  void
     */
    protected function _getPolygones(Base3DObject $object)
    {
        $newPolygones = $object->getPolygones();

        $this->_polygones = array_merge($this->_polygones, $newPolygones);

        // Add points unique to points-Array
        foreach ($newPolygones as $polygon) {
            $points = $polygon->getPoints();
            foreach ($points as $point) {
                if (!$point->isProcessed()) {
                    $point->processed();
                    array_push($this->_points, $point);
                }
            }
        }
    }

    /**
     * Caclulate Screen Coordinates
     *
     * Calculate screen coordinates for a point according to the perspektive
     * the renderer should display
     *
     * @param Point $point Point to process
     *
     * @return  void
     */
    abstract protected function _calculateScreenCoordiantes(Point $point);

    /**
     * Sort polygones
     *
     * Set the order the polygones will be displayed
     *
     * @return  void
     */
    abstract protected function _sortPolygones();

    /**
     * Add objects to renderer
     *
     * Add objects to renderer. Only objects which are added will be
     * displayed
     *
     * @param array $objects Array of objects
     *
     * @return  void
     */
    public function addObjects($objects)
    {
        if (is_array($objects)) {
            foreach ($objects as $object) {
                if ($object instanceof Base3DObject) {
                    $this->_getPolygones($object);
                }
            }
        } elseif ($objects instanceof Base3DObject) {
            $this->_getPolygones($objects);
        }
    }

    /**
     * Add objects to renderer
     *
     * Add objects to renderer. Only objects which are added will be
     * displayed
     *
     * @param array $lights Array of objects
     *
     * @return  void
     */
    public function addLights($lights)
    {
        $this->_lights = array_merge($this->_lights, $lights);
    }

    /**
     * Set image size
     *
     * Set the size of the destination image.
     *
     * @param integer $x Width
     * @param integer $y Height
     *
     * @return  void
     */
    public function setSize($x, $y)
    {
        $this->_size = array($x / 2, $y / 2);
    }

    /**
     * Set the Backgroundcolor
     *
     * Set the backgroundcolor of the destination image.
     *
     * @param Color $color Backgroundcolor
     *
     * @return  void
     */
    public function setBackgroundColor(Color $color)
    {
        $this->_background = $color;
    }

    /**
     * Set the quality of the shading
     *
     * Set the quality of the shading. Standard value is the maximum shading
     * quality the driver is able to render.
     *
     * @param integer $shading Shading quality
     *
     * @return  void
     */
    public function setShading($shading)
    {
        $this->_shading = min($this->_shading, (int) $shading);
    }

    /**
     * Set the driver
     *
     * Set the driver the image should be rendered with
     *
     * @param Driver $driver Driver to use
     *
     * @return  void
     */
    public function setDriver(Driver $driver)
    {
        $this->_driver = $driver;

        $this->setShading(max($driver->getSupportedShading()));
    }

    /**
     * Return polygon count
     *
     * Return the number of used polygones in this image
     *
     * @return  integer     Number of Polygones
     */
    public function getPolygonCount()
    {
        return count($this->_polygones);
    }

    /**
     * Return point count
     *
     * Return the number of used points in this image
     *
     * @return  integer     Number of Points
     */
    public function getPointCount()
    {
        return count($this->_points);
    }

    /**
     * Return light count
     *
     * Return the number of used lights in this image
     *
     * @return  integer     Number of Lights
     */
    public function getLightCount()
    {
        return count($this->_lights);
    }

    /**
     * Calculate the color of all polygones
     *
     * Let each polygon calculate his color based on the lights which are
     * registered for this image
     *
     * @return  void
     */
    protected function _calculatePolygonColors()
    {
        foreach ($this->_polygones as $polygon) {
            $polygon->calculateColor($this->_lights);
        }
    }

    /**
     * Calculate the colors of all points
     *
     * Let each point calculate his color based on his normale which is
     * calculated on his surrounding polygones and the lights which are
     * registered for this image
     *
     * @return  void
     */
    protected function _calculatePointColors()
    {
        foreach ($this->_polygones as $polygon) {
            $normale = $polygon->getNormale();
            $color = $polygon->getColor();

            $points = $polygon->getPoints();
            foreach ($points as $point) {
                $point->addVector($normale);
                $point->addColor($color);
            }
        }

        foreach ($this->_points as $point) {
            $point->calculateColor($this->_lights);
        }
    }

    /**
     * Draw all polygones
     *
     * Draw all polygones concerning the type of shading wich was set for the renderer
     *
     * @return  void
     */
    protected function _shade()
    {
        switch ($this->_shading) {
            case self::SHADE_NO:
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            case self::SHADE_FLAT:
                $this->_calculatePolygonColors();
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            case self::SHADE_GAUROUD:
                $this->_calculatePointColors();
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            default:
                throw new Exception('Shading type not supported.');
        }
    }

    /**
     * Render the image
     *
     * Render the image into the metioned file
     *
     * @param string $file Filename
     *
     * @return  void
     */
    public function render($file)
    {
        if (empty($this->_driver)) {
            return false;
        }

        // Calculate screen coordinates
        foreach ($this->_points as $point) {
            $this->_calculateScreenCoordiantes($point);
        }

        $this->_sortPolygones();

        // Draw background
        $this->_driver->createImage($this->_size[0] * 2, $this->_size[1] * 2);
        $this->_driver->setBackground($this->_background);

        // Create polygones in driver
        $this->_shade();

        // Save image
        $this->_driver->save($file);
    }
}
