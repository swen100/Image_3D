<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 3d Library
 *
 * PHP versions 5
 *
 * LICENSE: 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 0.1.0
 */


/**
 * Image_3D_Object
 *
 *
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @since      Class available since Release 0.1.0
 */
class Image_3D_Object implements Image_3D_Interface_Paintable {
	
	protected $_polygones;
	
	public function __construct() {
		$this->_polygones = array();
	}
	
	public function getPolygonCount() {
		return count($this->_polygones);
	}
	
	public function setColor(Image_3D_Color $color) {
		foreach ($this->_polygones as $polygon) $polygon->setColor($color);
	}
	
	public function setOption($option, $value) {
		foreach ($this->_polygones as $polygon) $polygon->setOption($option, $value);
	}

	public function transform(Image_3D_Matrix $matrix, $id = null) {
		
		if ($id === null) $id = substr(md5(microtime()), 0, 8);
		foreach ($this->_polygones as $polygon) $polygon->transform($matrix, $id);
	}
	
	public function getPolygones() {
		return $this->_polygones;
	}
	
	protected function _addPolygon(Image_3D_Polygon $polygon) {
		$this->_polygones[] = $polygon;
	}
	
    public function buildInzidenzGraph() {
        $polygons = $this->getPolygones();

        $surfaces = array();
        $edges = array();
        $points = array();

        $point_hash = array();
        $edge_hash = array();

        foreach ($polygons as $nr => $polygon) {
            $p_points = $polygon->getPoints();

            $last_index = false;
            $first_index = false;
            foreach ($p_points as $point) {
                // Add point to edge
                $p_p_hash = $point->__toString();
                if (isset($point_hash[$p_p_hash])) {
                    $p_p_index = $point_hash[$p_p_hash];
                } else {
                    $points[] = $point;
                    $p_p_index = count($points) - 1;
                    $point_hash[$p_p_hash] = $p_p_index;
                }

                // Add edge to surface
                if ($last_index !== false) {
                    $e_points = array($p_p_index, $last_index);
                    sort($e_points);
                    $p_e_hash = implode(' -> ', $e_points);
                    if (isset($edge_hash[$p_e_hash])) {
                        $surfaces[$nr][] = $edge_hash[$p_e_hash];
                    } else {
                        $edges[] = $e_points;
                        $edge_hash[$p_e_hash] = count($edges) - 1;
                        $surfaces[$nr][] = $edge_hash[$p_e_hash];
                    }
                } else {
                    $first_index = $p_p_index;
                }

                // Prepare last index for next iteration
                $last_index = $p_p_index;
            }

            // Close surface
            $e_points = array($first_index, $last_index);
            sort($e_points);
            $p_e_hash = implode(' -> ', $e_points);
            if (isset($edge_hash[$p_e_hash])) {
                $surfaces[$nr][] = $edge_hash[$p_e_hash];
            } else {
                $edges[] = $e_points;
                $edge_hash[$p_e_hash] = count($edges) - 1;
                $surfaces[$nr][] = $edge_hash[$p_e_hash];
            }
        }

        return array(
            'surfaces' => $surfaces,
            'edges' => $edges,
            'points' => $points,
        );
    }
}

?>
