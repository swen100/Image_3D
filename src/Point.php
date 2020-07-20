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
class Point extends Coordinate implements Enlightenable
{

    /**
     * @var array
     */
    protected $_option = [];
    
    /**
     * @var bool
     */
    protected $_processed = false;
    
    /**
     * @var Vector
     */
    protected $_normale;
    
    /**
     * @var array
     */
    protected $_vectors = [];
    
    /**
     * @var array
     */
    protected $_colors = [];
    
    /**
     * @var Color
     */
    protected $_color;

    /**
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value)
    {
        $this->_option[$option] = $value;
    }

    /**
     * @param array $lights
     * @return bool
     */
    public function calculateColor($lights): bool
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
        
        return true;
    }

    /**
     * @param \Image3D\Vector $vector
     */
    public function addVector(Vector $vector)
    {
        $this->_vectors[] = $vector;
    }

    /**
     * @return void
     */
    protected function calcNormale()
    {
        $this->_normale = new Vector(0, 0, 0);
        foreach ($this->_vectors as $vector) {
            $this->_normale->add($vector);
        }
        $this->_normale->unify();
    }

    /**
     * @return \Image3D\Vector
     */
    public function getNormale(): Vector
    {
        if (!($this->_normale instanceof Vector)) {
            $this->calcNormale();
        }
        return $this->_normale;
    }

    public function getPosition(): Vector
    {
        return new Vector($this->_x, $this->_y, $this->_z);
    }

    public function addColor(Color $color)
    {
        $this->_colors[] = $color;
    }

    /**
     * @return void
     */
    protected function mixColors()
    {
        $i = 0;
        $color = [0, 0, 0, 0];
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

    /**
     * @return Color
     */
    public function getColor(): Color
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
