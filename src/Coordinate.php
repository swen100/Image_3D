<?php

namespace Image3D;

/**
 * Base class for coordinates eg. points in the space
 *
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Coordinate
{
    /**
     * @var float X-coordinate
     */
    protected $_x;

    /**
     * @var float Y-coordinate
     */
    protected $_y;

    /**
     * @var float Z-coordinate
     */
    protected $_z;

    /**
     * @var string ID of the last transformation
     */
    protected $_lastTransformation;

    /**
     * @var bool flag to set if all relevant calculations for this point are done
     */
    protected $_processed;

    /**
     * @var array screen coordinates (2d) of 3d-point
     */
    protected $screenCoordinates = [];

    /**
     * Constructor for Image3D-Coordinate
     *
     * Create a Point with the given coordinates
     *
     * @param number $x X Coordinate, default 0.0
     * @param number $y Y Coordinate, default 0.0
     * @param number $z Z Coordinate, default 0.0
     *
     * @return Coordinate Instance of Coordinate
     */
    public function __construct($x = 0.0, $y = 0.0, $z = 0.0)
    {
        $this->_x = (float) $x;
        $this->_y = (float) $y;
        $this->_z = (float) $z;
    }

    /**
     * Transform the Coordinate
     *
     * Use a transformation-matrix to transform (move) the point
     *
     * @param Matrix $matrix Transformationmatrix
     * @param string $id     Transformationid
     *
     * @return void
     */
    public function transform(Matrix $matrix, $id = null)
    {
        // Point already transformed?
        if (($id !== null) && ($this->_lastTransformation === $id)) {
            return;
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

        $this->screenCoordinates = [];
    }

    /**
     * Set Coordinate as processed
     *
     * @return void
     */
    public function processed()
    {
        $this->_processed = true;
    }

    /**
     * Return if coordinate already was processsed
     *
     * @return bool processed
     */
    public function isProcessed()
    {
        return $this->_processed;
    }

    /**
     * Returns the X coordinate of the coordinate
     *
     * @return float X coordinate
     */
    public function getX()
    {
        return $this->_x;
    }

    /**
     * Returns the Y coordinate of the coordinate
     *
     * @return float Y coordinate
     */
    public function getY()
    {
        return $this->_y;
    }

    /**
     * Returns the Z coordinate of the coordinate
     *
     * @return float Z coordinate
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
     * @return void
     */
    public function setScreenCoordinates($x, $y)
    {
        $this->screenCoordinates = [(float) $x, (float) $y];
    }

    /**
     * Get screen coordinates
     *
     * Return an array with the screen coordinates
     * array (0 => (float) $x, 1 => (float) $y )
     *
     * @return array Screen coordinates
     */
    public function getScreenCoordinates(): array
    {
        return $this->screenCoordinates;
    }

    /**
     * Returns coordinate as string
     *
     * @return string Coordinate
     */
    public function __toString()
    {
        return sprintf('Coordinate: %2.f %2.f %2.f', $this->_x, $this->_y, $this->_z);
    }
}
