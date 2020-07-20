<?php

namespace Image3D\Paintable\Light;

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
class Ambient extends \Image3D\Paintable\Light
{

    public function __construct()
    {
        parent::__construct(0, 0, 0);
    }

    /**
     *
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
