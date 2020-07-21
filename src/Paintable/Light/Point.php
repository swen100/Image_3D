<?php

namespace Image3D\Paintable\Light;

use Image3D\Vector;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Point extends \Image3D\Paintable\Light
{

    protected $_color;
    protected $_falloff;
    protected $_distance;

    /**
     * @param number $x
     * @param number $y
     * @param number $z
     * @param array $parameter
     */
    public function __construct($x = 0.0, $y = 0.0, $z = 0.0, $parameter = [])
    {
        parent::__construct($x, $y, $z);

        $this->_falloff = max(0, (float) $parameter['falloff']);
        $this->_distance = (float) $parameter['distance'];
    }

    /**
     * @param \Image3D\Enlightenable $polygon
     * @return \Image3D\Color
     */
    public function getColor(\Image3D\Enlightenable $polygon): \Image3D\Color
    {
        $color = clone ($polygon->getColor());

        $light = new Vector($this->_x, $this->_y, $this->_z);
        $light->sub($polygon->getPosition());

        $distance = $light->length();

        if ($distance > $this->_distance) {
            return $color;
        }
        $factor = 1 - pow($distance / $this->_distance, $this->_falloff);

        $light->unify();
        $light->add(new Vector(0, 0, -1));

        $normale = $polygon->getNormale();

        $angle = abs(1 - $normale->getAngle($light));

        $color->addLight($this->_color, $angle * $factor);
        
        return $color;
    }
}
