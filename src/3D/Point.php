<?php

namespace Image3D;

/**
 * Image_3D_Point
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
class Point extends Coordinate implements Interface_Enlightenable
{

    protected $_option = [];
    protected $_lastTransformation;
    protected $_screenCoordinates;
    protected $_processed = false;
    protected $_normale;
    protected $_vectors;
    protected $_colors = [];
    protected $_color;

    public function setOption($option, $value)
    {
        $this->_option[$option] = $value;
    }

    public function calculateColor($lights)
    {
        if (!count($lights)) {
            $values = $this->getColor()->getValues();
            $this->_color = new Color(0, 0, 0, end($values));
            return false;
        }

        foreach ($lights as $light) {
            $this->_color = $light->getColor($this);
        }
        if (is_object($this->_color)) {
            $this->_color->calculateColor();
        }
    }

    public function addVector(Vector $vector)
    {
        $this->_vectors[] = $vector;
    }

    protected function calcNormale()
    {
        $this->_normale = new Vector(0, 0, 0);
        foreach ($this->_vectors as $vector) {
            $this->_normale->add($vector);
        }
        $this->_normale->unify();
    }

    public function getNormale()
    {
        if (!($this->_normale instanceof Vector)) {
            $this->calcNormale();
        }
        return $this->_normale;
    }

    public function getPosition()
    {
        return new Vector($this->_x, $this->_y, $this->_z);
    }

    public function addColor(Color $color)
    {
        $this->_colors[] = $color;
    }

    protected function mixColors()
    {
        $i = 0;
        $color = array(0, 0, 0, 0);
        foreach ($this->_colors as $c) {
            $values = $c->getValues();
            $color[0] += $values[0];
            $color[1] += $values[1];
            $color[2] += $values[2];
            $color[3] += $values[3];
            $i++;
        }
        $this->_color = new Color($color[0] / $i, $color[1] / $i, $color[2] / $i, $color[3] / $i);
    }

    public function getColor()
    {
        if ($this->_color === null) {
            $this->mixColors();
        }
        return $this->_color;
    }

    public function __toString()
    {
        return sprintf('Point: % 9.4f % 9.4f % 9.4f', $this->_x, $this->_y, $this->_z);
    }
}
