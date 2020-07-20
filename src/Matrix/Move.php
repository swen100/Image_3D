<?php

namespace Image3D\Matrix;

/**
 * Image_3D_Matrix_Move
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
class Move extends \Image3D\Matrix
{

    /**
     * @param array $parameter
     */
    public function __construct(array $parameter)
    {
        $this->setUnitMatrix();
        $this->setMoveMatrix((float) $parameter[0], (float) $parameter[1], (float) $parameter[2]);
    }
}
