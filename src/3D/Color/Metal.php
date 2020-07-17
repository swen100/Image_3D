<?php

namespace Image3D\Color;

/**
 * Image_3D_Color_Metal
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
class Image_3D_Color_Metal extends \Image3D\Color
{

    /**
     * @var float
     */
    protected $_metal;

    /**
     * 
     * @param number $metal
     */
    public function setMetal($metal)
    {
        $this->_metal = (float) $metal;
    }

    /**
     * @return void
     */
    protected function mixColor()
    {
        $this->_rgbaValue[0] = min(1, $this->_rgbaValue[0] * $this->_light[0] + $this->_metal * $this->_light[0]);
        $this->_rgbaValue[1] = min(1, $this->_rgbaValue[1] * $this->_light[1] + $this->_metal * $this->_light[1]);
        $this->_rgbaValue[2] = min(1, $this->_rgbaValue[2] * $this->_light[2] + $this->_metal * $this->_light[2]);
    }
}
