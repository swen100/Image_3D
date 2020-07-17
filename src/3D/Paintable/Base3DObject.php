<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
namespace Image3D\Paintable;

use Image3D\Color;
use Image3D\Matrix;

/**
 * Image_3D_Object
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
class Base3DObject implements \Image3D\Interface_Paintable
{
    
    protected $_polygones;
    
    public function __construct()
    {
        $this->_polygones = [];
    }
    
    public function getPolygonCount()
    {
        return count($this->_polygones);
    }
    
    public function setColor(Color $color)
    {
        foreach ($this->_polygones as $polygon) {
            $polygon->setColor($color);
        }
    }
    
    public function setOption($option, $value)
    {
        foreach ($this->_polygones as $polygon) {
            $polygon->setOption($option, $value);
        }
    }

    public function transform(Matrix $matrix, $id = null)
    {
        
        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }

        foreach ($this->_polygones as $polygon) {
            $polygon->transform($matrix, $id);
        }
    }
    
    public function getPolygones()
    {
        return $this->_polygones;
    }
    
    protected function _addPolygon(Polygon $polygon)
    {
        $this->_polygones[] = $polygon;
    }
    
    protected function _buildInzidenzGraph()
    {
        $polygons = $this->getPolygones();

        $surfaces = [];
        $edges    = [];
        $points   = [];

        $point_hash = [];
        $edge_hash  = [];

        foreach ($polygons as $nr => $polygon) {
            $p_points = $polygon->getPoints();

            $last_index  = false;
            $first_index = false;
            foreach ($p_points as $point) {
                // Add point to edge
                $p_p_hash = $point->__toString();
                if (isset($point_hash[$p_p_hash])) {
                    $p_p_index = $point_hash[$p_p_hash];
                } else {
                    $points[]  = $point;
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
                $edges[]              = $e_points;
                $edge_hash[$p_e_hash] = count($edges) - 1;
                $surfaces[$nr][]      = $edge_hash[$p_e_hash];
            }
        }

        return array(
            'surfaces' => $surfaces,
            'edges' => $edges,
            'points' => $points,
        );
    }

    public function subdivideSurfaces($factor = 1)
    {
        for ($i = 0; $i < $factor; ++$i) {
            $data = $this->_buildInzidenzGraph();

            // Additional hash maps
            $edge_surfaces = [];
            $edge_middles  = [];
            $point_edges   = [];
            
            // New calculated points
            $face_points = [];
            $edge_points = [];
            $old_points  = [];

            // Calculate "face points"
            foreach ($data['surfaces'] as $surface => $edges) {
                // Get all points
                $points = [];
                foreach ($edges as $edge) {
                    $points = array_merge($points, $data['edges'][$edge]);

                    $edge_surfaces[$edge][] = $surface;
                }
                $points = array_unique($points);

                // Calculate average
                $face_point  = array(0, 0, 0);
                $point_count = count($points);
                foreach ($points as $point) {
                    $face_point[0] += $data['points'][$point]->getX() / $point_count;
                    $face_point[1] += $data['points'][$point]->getY() / $point_count;
                    $face_point[2] += $data['points'][$point]->getZ() / $point_count;
                }
                
                // Create face point
                $face_points[$surface] = new \Image3D\Point($face_point[0], $face_point[1], $face_point[2]);
            }
            
            // Calculate "edge points"
            foreach ($data['edges'] as $edge => $points) {
                // Calculate middle of edge
                if (isset($edge_middles[$edge])) {
                    $edge_middle = $edge_middles[$edge];
                } else {
                    $edge_middle = array(0, 0, 0);
                    $point_count = count($points);
                    foreach ($points as $point) {
                        $point_edges[$point][] = $edge;

                        $edge_middle[0] += $data['points'][$point]->getX() / $point_count;
                        $edge_middle[1] += $data['points'][$point]->getY() / $point_count;
                        $edge_middle[2] += $data['points'][$point]->getZ() / $point_count;
                    }
                    $edge_middles[$edge] = $edge_middle;
                }

                // Calculate average of the adjacent faces
                $average_face = array(0, 0, 0);
                $point_count  = count($edge_surfaces[$edge]);
                foreach ($edge_surfaces[$edge] as $surface) {
                    $average_face[0] += $face_points[$surface]->getX() / $point_count;
                    $average_face[1] += $face_points[$surface]->getY() / $point_count;
                    $average_face[2] += $face_points[$surface]->getZ() / $point_count;
                }
                
                // Create edge point on this base
                $edge_points[$edge] = new \Image3D\Point(($average_face[0] + $edge_middle[0]) / 2, ($average_face[1] + $edge_middle[1]) / 2, ($average_face[2] + $edge_middle[2]) / 2);
            }

            // Reposition old vertices
            foreach ($data['points'] as $point => $value) {
                // Calculate average of midpoints of adjacent edges
                $r = array(0, 0, 0);

                $surfaces = [];

                $point_count = count($point_edges[$point]);

                foreach ($point_edges[$point] as $edge) {
                    $r[0] += $edge_middles[$edge][0] / $point_count;
                    $r[1] += $edge_middles[$edge][1] / $point_count;
                    $r[2] += $edge_middles[$edge][2] / $point_count;

                    $surfaces = array_merge($surfaces, $edge_surfaces[$edge]);
                }
                $surfaces = array_unique($surfaces);

                // Calculate average of surrounding face points
                $q = array(0, 0, 0);

                $surface_count = count($surfaces);
                foreach ($surfaces as $surface) {
                    $q[0] += $face_points[$surface]->getX() / $surface_count;
                    $q[1] += $face_points[$surface]->getY() / $surface_count;
                    $q[2] += $face_points[$surface]->getZ() / $surface_count;
                }

                // Create new edge point
                $n = count($point_edges[$point]);

                $old_points[$point] = new \Image3D\Point(
                    ($q[0] / $n) + ((2 * $r[0]) / $n) + (($value->getX() * ($n - 3)) / $n),
                    ($q[1] / $n) + ((2 * $r[1]) / $n) + (($value->getY() * ($n - 3)) / $n),
                    ($q[2] / $n) + ((2 * $r[2]) / $n) + (($value->getZ() * ($n - 3)) / $n)
                );
            }

            // Create polygones on new points
            $this->_polygones = [];
            foreach ($face_points as $surface => $face_point) {
                // Get all points for face
                $points = [];
                foreach ($data['surfaces'][$surface] as $edge) {
                    $points = array_merge($points, $data['edges'][$edge]);
                }
                $points = array_unique($points);

                // Create new polygones
                foreach ($points as $point) {
                    $edges = array_values(array_intersect($point_edges[$point], $data['surfaces'][$surface]));
                    $this->_addPolygon(new Polygon(
                        $old_points[$point],
                        $edge_points[$edges[0]],
                        $face_point,
                        $edge_points[$edges[1]]
                    ));
                }
            }

            // Debug output
            /*
            echo "\nFace points:\n";
            foreach ($face_points as $point) echo $point, "\n";

            echo "\nEdge points:\n";
            foreach ($edge_points as $point) echo $point, "\n";

            echo "\nRepositioned points:\n";
            foreach ($old_points as $point) echo $point, "\n";

            echo "\nCreated polygones:\n";
            foreach ($this->_polygones as $polygon) {
                echo $polygon;
            }
            */
        }
    }
}
