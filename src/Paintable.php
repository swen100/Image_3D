<?php

namespace Image3D;

/**
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
interface Paintable
{
    
    /**
     * @return int
     */
    public function getPolygonCount(): int;

    /**
     * @param \Image3D\Color $color
     */
    public function setColor(Color $color);

    /**
     * @param string $option
     * @param mixed $value
     */
    public function setOption($option, $value);

    /**
     * @param \Image3D\Matrix $matrix
     * @param string $id
     */
    public function transform(Matrix $matrix, $id = null);
}
