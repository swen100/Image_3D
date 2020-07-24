<?php

namespace Image3D;

use Image3D\Paintable\Base3DObject;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
abstract class Renderer
{

    /**
     * @var array Worlds polygones
     */
    protected $_polygones = [];

    /**
     * @var array Worlds points
     */
    protected $_points = [];

    /**
     * @var array Worlds lights
     */
    protected $_lights = [];

    /**
     * @var Driver Driver we use
     */
    protected $_driver;

    /**
     * @var array Size of the Image
     */
    protected $_size = [0, 0];

    /**
     * @var Color Backgroundcolor
     */
    protected $bgColorObj;

    /**
     * @var integer Type of Shading used (0: no shade, 1: flat, 2: gauroud, 3: phong)
     */
    protected $_shading = 3;

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
     * @var array
     */
    private $_objects = [];

    /**
     * Reset all changeable variables
     *
     * Initialises the environment
     *
     * @return void
     */
    public function reset()
    {
        $this->_objects = [];
        $this->_polygones = [];

        $this->_points = [];
        $this->_lights = [];
        $this->_size = [0, 0];

        unset($this->bgColorObj);
        unset($this->_driver);

        $this->_shading = self::SHADE_PHONG;
    }

    /**
     * Get and merge polygones
     *
     * Get polygones and points from an object and merge them unique to local polygon- and pointarrays.
     *
     * @param Base3DObject $object Object to merge
     * @return void
     */
    protected function getPolygones(Base3DObject $object)
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
     * Calculate screen coordinates for a point according to the perspektive the renderer should display.
     *
     * @param Point $point Point to process
     * @return void
     */
    abstract protected function calculateScreenCoordiantes(Point $point);

    /**
     * Sort polygones
     *
     * Sets the order of the polygones.
     *
     * @return void
     */
    abstract protected function sortPolygones();

    /**
     * Add objects to renderer. Only objects which are added will be displayed.
     *
     * @param array|Base3DObject $objects Array of objects or alreaady a Base3DObject
     * @return void
     */
    public function addObjects($objects)
    {
        if (is_array($objects)) {
            foreach ($objects as $object) {
                if ($object instanceof Base3DObject) {
                    $this->getPolygones($object);
                }
            }
        } elseif ($objects instanceof Base3DObject) {
            $this->getPolygones($objects);
        }
    }

    /**
     * Add objects to renderer. Only objects which are added will be displayed.
     *
     * @param array $lights Array of objects
     * @return void
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
     * @return void
     */
    public function setSize($x, $y)
    {
        $this->_size = [$x / 2, $y / 2];
    }

    /**
     * Set the backgroundcolor of the destination image.
     *
     * @param Color $color Backgroundcolor
     * @return void
     */
    public function setBackgroundColor(Color $color)
    {
        $this->bgColorObj = $color;
    }

    /**
     * Set the quality of the shading. Standard value is the maximum shading quality the driver is able to render.
     *
     * @param integer $shading Shading quality
     * @return void
     */
    public function setShading($shading)
    {
        $this->_shading = min($this->_shading, (int) $shading);
    }

    /**
     * Set the driver the image should be rendered with.
     *
     * @param Driver $driver Driver to use
     * @return void
     */
    public function setDriver(Driver $driver)
    {
        $this->_driver = $driver;

        $this->setShading(max($driver->getSupportedShading()));
    }

    /**
     * Return polygon count
     *
     * Return the number of used polygones in this image.
     *
     * @return integer     Number of Polygones
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
     * @return integer Number of Points
     */
    public function getPointCount()
    {
        return count($this->_points);
    }

    /**
     * Return light count
     *
     * Returns the number of used lights in this image.
     *
     * @return integer Number of Lights
     */
    public function getLightCount()
    {
        return count($this->_lights);
    }

    /**
     * Calculate the color of all polygones
     *
     * Let each polygon calculate his color based on the lights which are registered for this image.
     *
     * @return void
     */
    protected function calculatePolygonColors()
    {
        foreach ($this->_polygones as $polygon) {
            $polygon->calculateColor($this->_lights);
        }
    }

    /**
     * Calculate the colors of all points
     *
     * Let each point calculate his color based on his normale which is calculated on his surrounding polygones and
     * the lights which are registered for this image
     *
     * @return void
     */
    protected function calculatePointColors()
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
     * Draw all polygones concerning the type of shading wich was set for the renderer
     *
     * @return void
     */
    protected function shade()
    {
        switch ($this->_shading) {
            case self::SHADE_NO:
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            case self::SHADE_FLAT:
                $this->calculatePolygonColors();
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            case self::SHADE_GAUROUD:
                $this->calculatePointColors();
                foreach ($this->_polygones as $polygon) {
                    $this->_driver->drawPolygon($polygon);
                }
                break;

            default:
                throw new \Exception('Shading type not supported.');
        }
    }

    /**
     * Render the image into the metioned file
     *
     * @param string $file Filename
     * @return bool
     */
    public function render($file): bool
    {
        if (empty($this->_driver)) {
            return false;
        }

        // Calculate screen coordinates
        foreach ($this->_points as $point) {
            $this->calculateScreenCoordiantes($point);
        }

        $this->sortPolygones();

        // Draw background
        $this->_driver->createImage($this->_size[0] * 2, $this->_size[1] * 2);
        $this->_driver->setBackground($this->bgColorObj);

        // Create polygones in driver
        $this->shade();

        // Save image
        $this->_driver->save($file);
        
        return true;
    }
}
