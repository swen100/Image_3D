<?php

namespace Image3D;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Line extends Vector
{
    protected $_direction;

    public function __construct($x, $y, $z, Vector $direction)
    {
        parent::__construct($x, $y, $z);
        $this->_direction = $direction;
    }

    public function calcPoint($t)
    {
        $t = (float) $t;

        return new Coordinate(
            $this->getX() + $t * $this->_direction->getX(),
            $this->getY() + $t * $this->_direction->getY(),
            $this->getZ() + $t * $this->_direction->getZ()
        );
    }

    public function setDirection(Vector $direcetion)
    {
        $this->_direction = $direcetion;
    }

    public function getDirection()
    {
        return $this->_direction;
    }

    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->getDirection()->__toString();
    }
}
