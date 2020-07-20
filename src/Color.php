<?php

namespace Image3D;

/**
 * Base class for colors and textures.
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
class Color
{

    /**
     * Color values
     *
     * @var array
     */
    protected $_rgbaValue = [];

    /**
     * Array with lights which influence this color
     *
     * @var array
     */
    protected $_lights = [];

    /**
     * Resulting light for this color
     *
     * @var array
     */
    protected $_light = [0, 0, 0];

    /**
     * Optinal value for reflection
     *
     * @var float
     */
    protected $_reflection;

    /**
     * Constructor for Image_3D_Color
     *
     * All colors accept values in integer (0 - 255) or float (0 - 1)
     *
     * @param number $red   red
     * @param number $green green
     * @param number $blue  blue
     * @param number $alpha alpha
     * @param float  $reflection
     *
     * @return Color Instance of Color
     */
    public function __construct($red = 0., $green = 0., $blue = 0., $alpha = 0., $reflection = null)
    {
        $arglist = func_get_args();
        $argcount = func_num_args();

        $this->_rgbaValue[0] = (float) min(1, max(0, (float) $red / (is_float($red) ? 1 : 255)));
        $this->_rgbaValue[1] = (float) min(1, max(0, (float) $red / (is_float($green) ? 1 : 255)));
        $this->_rgbaValue[2] = (float) min(1, max(0, (float) $red / (is_float($blue) ? 1 : 255)));
        $this->_rgbaValue[3] = (float) min(1, max(0, (float) $red / (is_float($alpha) ? 1 : 255)));
        
        for ($i = 0; $i < 4; $i++) {
            if ($i >= $argcount) {
                $this->_rgbaValue[$i] = 0;
            } elseif (is_int($arglist[$i])) {
                $this->_rgbaValue[$i] = (float) min(1, max(0, (float) $arglist[$i] / 255));
            } elseif (is_float($arglist[$i])) {
                $this->_rgbaValue[$i] = (float) min(1, max(0, $arglist[$i]));
            } else {
                $this->_rgbaValue[$i] = 0;
            }
        }

        $this->setReflection($reflection);
    }

    /**
     * Apply alphavalue to color
     *
     * Apply alpha value to color. It may be int or float. 255 / 1. means full
     * oppacity
     *
     * @param mixed $alpha Alphavalue
     *
     * @return void
     */
    public function mixAlpha($alpha = 1.)
    {
        if (is_int($alpha)) {
            $this->_rgbaValue[3] *= (float) min(1, max(0, (float) $alpha / 255));
        } else {
            $this->_rgbaValue[3] *= (float) min(1, max(0, (float) $alpha));
        }
    }

    /**
     * sets reflection intensity
     *
     * @param float $reflection reflection
     *
     * @return void
     */
    public function setReflection($reflection)
    {
        $this->_reflection = min(1, max(0, (float) $reflection));
    }

    /**
     * return reflection intensity
     *
     * @return float           reflection
     */
    public function getReflection()
    {
        if (!isset($this->_reflection)) {
            return 0;
        } else {
            return $this->_reflection;
        }
    }

    /**
     * Return an array with RGBA-values
     *  0 =>    (float) red
     *  1 =>    (float) green
     *  2 =>    (float) blue
     *  3 =>    (float) alpha
     *
     * @return array RGBA-Values
     */
    public function getValues()
    {
        return $this->_rgbaValue;
    }

    /**
     * Add an light which influence the object this color is created for
     *
     * @param Color $color Lightcolor
     * @param mixed $intensity Intensity
     *
     * @return void
     */
    public function addLight(Color $color, $intensity = .5)
    {
        $this->_lights[] = array($intensity, $color);
    }

    /**
     * Calculate light depending an all lights which influence this object
     *
     * @return void
     */
    protected function calcLights()
    {
        foreach ($this->_lights as $light) {
            list($intensity, $color) = $light;

            $colorArray = $color->getValues();

            $this->_light[0] += $colorArray[0] * $intensity * (1 - $colorArray[3]);
            $this->_light[1] += $colorArray[1] * $intensity * (1 - $colorArray[3]);
            $this->_light[2] += $colorArray[2] * $intensity * (1 - $colorArray[3]);
        }
    }

    /**
     * Mix Color with light
     * Recalculate color depending on the lights.
     *
     * @return void
     */
    protected function mixColor()
    {
        $this->_rgbaValue[0] = min(1, $this->_rgbaValue[0] * $this->_light[0]);
        $this->_rgbaValue[1] = min(1, $this->_rgbaValue[1] * $this->_light[1]);
        $this->_rgbaValue[2] = min(1, $this->_rgbaValue[2] * $this->_light[2]);
    }

    /**
     * Calculate color depending on the lights
     *
     * @return void
     */
    public function calculateColor()
    {
        if (!count($this->_lights)) {
            $this->_rgbaValue = [0, 0, 0, $this->_rgbaValue[3]];
            return;
        }

        $this->calcLights();
        $this->mixColor();
    }

    /**
     * Merge color with other colors
     *
     * @return Color
     */
    public function merge($colors): self
    {
        $count = 0;
        foreach ($colors as $color) {
            if (!($color instanceof Color)) {
                continue;
            }

            $values = $color->getValues();

            $this->_rgbaValue[0] += $values[0];
            $this->_rgbaValue[1] += $values[1];
            $this->_rgbaValue[2] += $values[2];
            $this->_rgbaValue[3] += $values[3];

            ++$count;
        }

        $this->_rgbaValue[0] /= $count;
        $this->_rgbaValue[1] /= $count;
        $this->_rgbaValue[2] /= $count;
        $this->_rgbaValue[3] /= $count;

        return $this;
    }

    /**
     * Return a string representation of the color
     *
     * @return string String representation of color
     */
    public function __toString()
    {
        return sprintf(
            "Color: r %.2f g %.2f b %.2f a %.2f\n",
            $this->_rgbaValue[0],
            $this->_rgbaValue[1],
            $this->_rgbaValue[2],
            $this->_rgbaValue[3]
        );
    }
}
