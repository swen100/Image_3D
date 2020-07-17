<?php

namespace Image3D;

/**
 * Base class for coordinates eg. points in the space
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
class Coordinate
{
    /**
     * X Coordiante
     *
     * @var float
     */
    protected $_x;

    /**
     * Y Coordiante
     *
     * @var float
     */
    protected $_y;

    /**
     * Z Coordiante
     *
     * @var float
     */
    protected $_z;

    /**
     * ID of the last transformation
     *
     * @var string
     */
    protected $_lastTransformation;

    /**
     * Variable saves if all relevant calculations for this point are done
     *
     * @var boolean
     */
    protected $_processed;

    /**
     * Screen coordinates (2d) of 3d-point
     *
     * @var array
     */
    protected $_screenCoordinates;

    /**
     * Constructor for Image_3D_Coordinate
     *
     * Create a Point with the given coordinates
     *
     * @param number $x X Coordinate
     * @param number $y Y Coordinate
     * @param number $z Z Coordinate
     *
     * @return  Coordinate         Instance of Coordinate
     */
    public function __construct($x, $y, $z)
    {
        $this->_x = (float) $x;
        $this->_y = (float) $y;
        $this->_z = (float) $z;
    }

    /**
     * Transform the Coordinate
     *
     * Use a transformationmatrix to transform (move) the point
     *
     * @param Matrix $matrix Transformationmatrix
     * @param string          $id     Transformationid
     *
     * @return  void
     */
    public function transform(Matrix $matrix, $id = null)
    {
        // Point already transformed?
        if (($id !== null) && ($this->_lastTransformation === $id)) {
            return false;
        }

        $this->_lastTransformation = $id;

        $point = clone($this);

        $this->_x = $point->getX() * $matrix->getValue(0, 0) +
                $point->getY() * $matrix->getValue(1, 0) +
                $point->getZ() * $matrix->getValue(2, 0) +
                $matrix->getValue(3, 0);
        $this->_y = $point->getX() * $matrix->getValue(0, 1) +
                $point->getY() * $matrix->getValue(1, 1) +
                $point->getZ() * $matrix->getValue(2, 1) +
                $matrix->getValue(3, 1);
        $this->_z = $point->getX() * $matrix->getValue(0, 2) +
                $point->getY() * $matrix->getValue(1, 2) +
                $point->getZ() * $matrix->getValue(2, 2) +
                $matrix->getValue(3, 2);

        $this->_screenCoordinates = null;
    }

    /**
     * Set Coordinate processed
     *
     * Store the coordinate as processed
     *
     * @return  void
     */
    public function processed()
    {
        $this->_processed = true;
    }

    /**
     * Coordinate already processed
     *
     * Return if coordinate already was processsed
     *
     * @return  bool    processed
     */
    public function isProcessed()
    {
        return $this->_processed;
    }

    /**
     * Return X coordinate
     *
     * Returns the X coordinate of the coordinate
     *
     * @return  float    X coordinate
     */
    public function getX()
    {
        return $this->_x;
    }

    /**
     * Return Y coordinate
     *
     * Returns the Y coordinate of the coordinate
     *
     * @return  float    Y coordinate
     */
    public function getY()
    {
        return $this->_y;
    }

    /**
     * Return Z coordinate
     *
     * Returns the Z coordinate of the coordinate
     *
     * @return  float    Z coordinate
     */
    public function getZ()
    {
        return $this->_z;
    }

    /**
     * Set precalculated screen coordinates
     *
     * Store the screen coordinates calculated by the Renderer
     *
     * @param float $x X coordinate
     * @param float $y Y coordinate
     *
     * @return  void
     */
    public function setScreenCoordinates($x, $y)
    {
        $this->_screenCoordinates = array((float) $x, (float) $y);
    }

    /**
     * Get screen coordinates
     *
     * Return an array with the screen coordinates
     * array (     0 =>    (float) $x,
      1 =>    (float) $y )
     *
     * @return  array    Screen coordinates
     */
    public function getScreenCoordinates()
    {
        return $this->_screenCoordinates;
    }

    /**
     * Returns coordinate as string
     *
     * @return  string    Coordinate
     */
    public function __toString()
    {
        return sprintf('Coordinate: %2.f %2.f %2.f', $this->_x, $this->_y, $this->_z);
    }
}
