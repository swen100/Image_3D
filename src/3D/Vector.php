<?php

namespace Image3D;

/**
 * Image_3D_Vector
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
class Vector extends Coordinate
{

    protected $_length;

    public function getAngle(Vector $vector)
    {
        $length = $vector->length() * $this->length();
        if ($length < 0.0001) {
            return 1;
        }

        return abs(acos($this->scalar($vector) / $length) / M_PI - .5) * 2;
    }

    public function getSide(Vector $vector)
    {
//        $vector->unify();
//        $this->unify();
        return $this->scalar($vector);
    }

    public function unify()
    {
        if ($this->length() == 0) {
            return false;
        }
        if ($this->_length == 1) {
            return $this;
        }

        $this->_x /= $this->_length;
        $this->_y /= $this->_length;
        $this->_z /= $this->_length;
        $this->_length = 1;
        return $this;
    }

    public function length()
    {
        if (empty($this->_length)) {
            $this->_length = sqrt(pow($this->_x, 2) + pow($this->_y, 2) + pow($this->_z, 2));
        }
        return $this->_length;
    }

    public function add(Coordinate $vector)
    {
        $this->_x += $vector->getX();
        $this->_y += $vector->getY();
        $this->_z += $vector->getZ();
        $this->_length = null;
        return $this;
    }

    public function sub(Coordinate $vector)
    {
        $this->_x -= $vector->getX();
        $this->_y -= $vector->getY();
        $this->_z -= $vector->getZ();
        $this->_length = null;
        return $this;
    }

    public function multiply($scalar)
    {
        if ($scalar instanceof Vector) {
            return $this->scalar($scalar);
        }

        $this->_x *= $scalar;
        $this->_y *= $scalar;
        $this->_z *= $scalar;
        $this->_length = null;
        return $this;
    }

    public function scalar(Coordinate $vector)
    {
        return (($this->_x * $vector->getX()) +
                ($this->_y * $vector->getY()) +
                ($this->_z * $vector->getZ()));
    }

    public function crossProduct(Coordinate $vector)
    {
        return new Vector(
            $this->getY() * $vector->getZ() - $this->getZ() * $vector->getY(),
            $this->getZ() * $vector->getX() - $this->getX() * $vector->getZ(),
            $this->getX() * $vector->getY() - $this->getY() * $vector->getX()
        );
    }
}
