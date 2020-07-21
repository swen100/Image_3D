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
class Spotlight extends \Image3D\Paintable\Light
{

    /**
     * @var Vector
     */
    protected $_direction;
    
    /**
     * @var float
     */
    protected $_angle;
    
    /**
     * @var int
     */
    protected $_float;

    /**
     * @param number $x
     * @param number $y
     * @param number $z
     * @param array $parameter ['aim'=>[float,float,float], 'angle'=>float, 'float'=>int]
     */
    public function __construct($x = 0.0, $y = 0.0, $z = 0.0, array $parameter = [])
    {
        parent::__construct($x, $y, $z);

        if (!isset($parameter['aim'])) {
            $parameter['aim'] = [0,0,0];
        }
        $aim = new Vector($parameter['aim'][0] ?? 0, $parameter['aim'][1] ?? 0, $parameter['aim'][2] ?? 0);
        $light = new Vector($this->_x, $this->_y, $this->_z);
        $light->sub($aim);
        $this->_direction = $light;
        $this->_direction->unify();

        $this->_angle = deg2rad($parameter['angle'] ?? 0) / 2;
        $this->_float = (int) ($parameter['float'] ?? 1);
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
        $light->unify();

        $angle = $light->getAngle($this->_direction);
        if ($angle > $this->_angle) {
            return $color;
        }

        $factor = 1 - pow($angle / $this->_angle, $this->_float);

        $light->add(new Vector(0, 0, -1));
        $normale = $polygon->getNormale();

        $angle = abs(1 - $normale->getAngle($light));

        $color->addLight($this->_color, $angle * $factor);
        
        return $color;
    }
}
