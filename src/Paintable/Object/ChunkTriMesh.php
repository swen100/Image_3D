<?php

namespace Image3D\Paintable\Object;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ChunkTriMesh extends Chunk
{

    /**
     * @var DsObject
     */
    protected $object;

    /**
     * @param number $type
     * @param string $content
     * @param \Image3D\Paintable\Object\DsObject $object
     */
    public function __construct($type, string $content, DsObject $object)
    {
        parent::__construct($type, $content);

        $this->object = $object;

        $this->readChunks();
        $this->getPoints();
        $this->getFaces();
    }

    protected function getPoints()
    {
        $vertexlists = $this->getChunksByType(Chunk::TRI_VERTEXL);
        
        foreach ($vertexlists as $vertexlist) {
            $points = $vertexlist->getContent();
            $count = $this->getWord(substr($points, 0, 2));
            $points = substr($points, 2);

            for ($i = 0; $i < $count; ++$i) {
                $x = $this->getFloat(substr($points, 0, 4));
                $y = $this->getFloat(substr($points, 4, 4));
                $z = $this->getFloat(substr($points, 8, 4));
                $this->object->newPoint($x, $y, $z);
                $points = substr($points, 12);
            }
        }
    }

    protected function getFaces()
    {
        $facelists = $this->getChunksByType(Chunk::TRI_FACEL1);
        foreach ($facelists as $facelist) {
            $faces = $facelist->getContent();
            $count = $this->getWord(substr($faces, 0, 2));
            $faces = substr($faces, 2);

            for ($i = 0; $i < $count; $i++) {
                $p1 = $this->getWord(substr($faces, 0, 2));
                $p2 = $this->getWord(substr($faces, 2, 2));
                $p3 = $this->getWord(substr($faces, 4, 2));
                $this->object->newPolygon($p1, $p2, $p3);
                $faces = substr($faces, 8);
            }
        }
    }

    protected function getTranslations()
    {
        $translists = $this->getChunksByType(Chunk::TRI_LOCAL);
        foreach ($translists as $translist) {
            $trans = $translist->getContent();
            echo "Trans: " . strlen($trans), "\n";
        }
    }

    public function debug()
    {
        parent::debug();
        $numPoints = $this->object->getNumPoints();
        printf("Trimesh with %d (0x%04x) points - Pointsize: %.2f\n", $numPoints, $numPoints, $this->size / $numPoints);
    }
}
