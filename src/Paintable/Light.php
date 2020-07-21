<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Image3D\Paintable;

use Image3D\Vector;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Light extends \Image3D\Coordinate implements \Image3D\Paintable
{
    
    /**
     * @var \Image3D\Color
     */
    protected $_color;
    
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * @return int 1
     */
    public function getPolygonCount(): int
    {
        return 0;
    }
    
    /**
     * @param \Image3D\Color $color
     */
    public function setColor(\Image3D\Color $color)
    {
        $this->_color = $color;
    }

    /**
     * @return \Image3D\Color
     */
    public function getRawColor()
    {
        return $this->_color;
    }
    
    /**
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }
    
    /**
     * @param \Image3D\Enlightenable $polygon
     * @return \Image3D\Color
     */
    public function getColor(\Image3D\Enlightenable $polygon): \Image3D\Color
    {
        $color = clone ($polygon->getColor());
        
        // Create vector from polygons point to light source
        $light = new Vector($this->_x, $this->_y, $this->_z);
        $light->sub($polygon->getPosition());
        
        // Compare with polygones normale vector
        $normale = $polygon->getNormale();
        $angle = $normale->getAngle($light);
        
        // Use angle as light intensity
        $color->addLight($this->_color, $angle);
        
        return $color;
    }
}
