<?php

namespace Image3D;

/**
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
interface Enlightenable
{
    /**
     * Get Color from object
     *
     * Return the color for the object
     *
     * @return \Image3D\Color Color of object
     */
    public function getColor(): Color;
    
    /**
     * Get normale vector from object
     *
     * Return normale vector for the object
     *
     * @return \Image3D\Vector Normale vector
     */
    public function getNormale(): Vector;
    
    /**
     * Get position from object
     *
     * Return position for the object
     *
     * @return \Image3D\Vector
     */
    public function getPosition(): Vector;
}
