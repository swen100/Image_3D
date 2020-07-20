<?php

namespace Image3D\Renderer;

use Image3D\Point;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Perspectively extends \Image3D\Renderer
{

    /**
     * Caclulate Screen Coordinates
     *
     * Calculate perspectively screen coordinates for a point
     *
     * @param Point $point Point to process
     *
     * @return void
     */
    protected function calculateScreenCoordiantes(Point $point)
    {
        $viewpoint = 500.;
        $distance = 500.;

        $point->setScreenCoordinates($viewpoint * $point->getX() / ($point->getZ() + $distance) + $this->_size[0], $viewpoint * $point->getY() / ($point->getZ() + $distance) + $this->_size[1]);
    }

    /**
     * Sort polygones
     *
     * Sort the polygones depending on their medium depth
     *
     * @return void
     */
    protected function sortPolygones()
    {
        $polygoneDepth = array();
        foreach ($this->_polygones as $polygon) {
            $polygoneDepth[] = $polygon->getMidZ();
        }

        array_multisort($polygoneDepth, SORT_DESC, SORT_NUMERIC, $this->_polygones);
    }
}
