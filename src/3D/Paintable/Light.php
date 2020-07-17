<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Image3D\Paintable;

use Image3D\Vector;

/**
 * Image_3D_Light
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
class Light extends \Image3D\Coordinate implements \Image3D\Interface_Paintable
{
    
    protected $_color;
    
    public function __construct($x, $y, $z)
    {
        parent::__construct($x, $y, $z);
        
        $this->_color = null;
        $this->_position = null;
    }
    
    public function getPolygonCount()
    {
        return 0;
    }
    
    public function setColor(\Image3D\Color $color)
    {
        $this->_color = $color;
    }

    public function getRawColor()
    {
        return $this->_color;
    }
    
    public function setOption($option, $value)
    {
        $this->_option[$option] = $value;
    }
    
    public function getColor(\Image3D\Interface_Enlightenable $polygon)
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
