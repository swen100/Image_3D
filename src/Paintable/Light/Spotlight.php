<?php

namespace Image3D\Paintable\Light;

use \Image3D\Vector;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class /*Image_3D_Light_*/Spotlight extends \Image3D\Paintable\Light
{

    protected $_direction;
    protected $_angle;
    protected $_float;

    /**
     *
     * @param number $x
     * @param number $y
     * @param number $z
     * @param array $parameter
     */
    public function __construct($x, $y, $z, $parameter)
    {
        parent::__construct($x, $y, $z);

        $aim = new Vector($parameter['aim'][0], $parameter['aim'][1], $parameter['aim'][2]);
        $light = new Vector($this->_x, $this->_y, $this->_z);
        $light->sub($aim);
        $this->_direction = $light;
        $this->_direction->unify();

        $this->_angle = deg2rad($parameter['angle']) / 2;
        $this->_float = (int) $parameter['float'];
    }

    /**
     *
     * @param \Image3D\Enlightenable $polygon
     * @return \Image3D\Color
     */
    public function getColor(\Image3D\Enlightenable $polygon)
    {
        $color = clone ($polygon->getColor());

        $light = new Vector($this->_x, $this->_y, $this->_z);
        $light->sub($polygon->getPosition());
        $light->unify();

        $angle = $light->getAngle($this->_direction);
        if ($angle > $this->_angle) {
            return $color;
        }

        if ($this->_float) {
            $factor = 1 - pow($angle / $this->_angle, $this->_float);
        } else {
            $factor = 1;
        }

        $light->add(new Vector(0, 0, -1));
        $normale = $polygon->getNormale();

        $angle = abs(1 - $normale->getAngle($light));

        $color->addLight($this->_color, $angle * $factor);
        
        return $color;
    }
}
