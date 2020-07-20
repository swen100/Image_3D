<?php

namespace Image3D\Matrix;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Move extends \Image3D\Matrix
{

    /**
     * @param array{float, float, float} $parameter
     */
    public function __construct(array $parameter)
    {
        parent::__construct();
        $this->setMoveMatrix($parameter[0] ?? 0, $parameter[1] ?? 0, $parameter[2] ?? 0);
    }
}
