<?php

namespace Image3D;

/**
 * Image_3D_Interface_Enlightenable
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
interface Interface_Enlightenable
{
    /**
     * Get Color from object
     *
     * Return the color for the object
     *
     * @return Color Color of object
     */
    public function getColor(): Color;
    
    /**
     * Get normale vector from object
     *
     * Return normale vector for the object
     *
     * @return Vector Normale vector
     */
    public function getNormale(): Vector;
    
    /**
     * Get position from object
     *
     * Return position for the object
     *
     * @return Vector
     */
    public function getPosition(): Vector;
}
