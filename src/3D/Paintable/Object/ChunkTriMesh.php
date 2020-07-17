<?php

namespace Image3D\Paintable\Object;

class /*Image_3D_*/ChunkTriMesh extends /*Image_3D_*/ Chunk
{

    protected $matrix;
    protected $object;

    public function __construct($type, $content, $object)
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

            for ($i = 0; $i < $count; $i++) {
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
        printf("Trimesh with %d (0x%04x) points - Pointsize: %.2f\n", $this->pointCount, $this->pointCount, $this->size / $this->pointCount);
    }
}
