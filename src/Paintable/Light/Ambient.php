<?php

namespace Image3D\Paintable\Light;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Ambient extends \Image3D\Paintable\Light
{
    /**
     * @param \Image3D\Enlightenable $polygon
     * @return \Image3D\Color
     */
    public function getColor(\Image3D\Enlightenable $polygon): \Image3D\Color
    {
        $color = clone ($polygon->getColor());
        $color->addLight($this->_color, 1);
        
        return $color;
    }
}
