<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Image3D\Matrix;

/**
 * Image_3D_Matrix_Scale
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
class Scale extends \Image3D\Matrix
{

    public function __construct($parameter)
    {
        $this->setUnitMatrix();
        $this->setScaleMatrix((float) @$parameter[0], (float) @$parameter[1], (float) @$parameter[2]);
    }
}
