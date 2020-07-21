<?php

namespace Image3D\Paintable\Object;

use Image3D\Matrix;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Ds extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var string
     */
    protected $_file;
    
    /**
     * @var array<DsObject>
     */
    protected $_objects = [];
    
    /**
     * @var Chunk
     */
    protected $_chunks;

    /**
     * @param string $file
     * @throws \Exception
     */
    public function __construct($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new \Exception("3ds file ($file) could not be loaded.");
        }
        $this->_file = $file;

        $this->readChunks();
    }

    /**
     * @return void
     */
    protected function readChunks()
    {
        $this->_chunks = new Chunk(Chunk::MAIN3DS, substr(file_get_contents($this->_file), 6));
        $this->_chunks->readChunks();

        $editor = $this->_chunks->getFirstChunkByType(Chunk::EDIT3DS);
        $editor->readChunks();

        /** @var array<\Image3D\Paintable\Object\Chunk> $objects */
        $objects = $editor->getChunksByType(Chunk::EDIT_OBJECT);

        foreach ($objects as $object) {
            $chunkObj = new ChunkObject($object->getType(), $object->getContent());
            $chunkObj->readChunks($this);
        }
    }

    /**
     * @param string $id
     * @return DsObject
     */
    public function addObject($id)
    {
        $id = (string) $id;
        $this->_objects[$id] = new DsObject();
        return $this->_objects[$id];
    }

    /**
     * @return array
     */
    public function getObjectIDs(): array
    {
        return array_keys($this->_objects);
    }

    /**
     * @param string $id
     * @return false|DsObject
     */
    public function getObject($id)
    {
        if (!isset($this->_objects[$id])) {
            return false;
        }
        return $this->_objects[$id];
    }

//    /**
//     * @return void
//     */
//    public function paint()
//    {
//        foreach ($this->_objects as $object) {
//            $object->paint();
//        }
//    }

    /**
     * @return int
     */
    public function getPolygonCount(): int
    {
        $count = 0;
        foreach ($this->_objects as $object) {
            $count += $object->getPolygonCount();
        }
        return $count;
    }

    /**
     * Sets the color for every created DsObject.
     * 
     * @param \Image3D\Color $color
     * @return void
     */
    public function setColor(\Image3D\Color $color)
    {
        foreach ($this->_objects as $object) {
            $object->setColor($color);
        }
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return void
     */
    public function setOption($option, $value)
    {
        foreach ($this->_objects as $object) {
            $object->setOption($option, $value);
        }
    }

    /**
     * @param Matrix $matrix
     * @param string $id
     * @return void
     */
    public function transform(Matrix $matrix, $id = null)
    {
        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }
        foreach ($this->_objects as $object) {
            $object->transform($matrix, $id);
        }
    }

    /**
     * @param number $factor
     * @return void
     */
    public function subdivideSurfaces($factor = 1)
    {
        foreach ($this->_objects as $object) {
            $object->subdivideSurfaces($factor);
        }
    }

    /**
     * @return array
     */
    public function getPolygones(): array
    {
        $polygones = [];
        foreach ($this->_objects as $object) {
            $polygones = array_merge($polygones, $object->getPolygones());
        }
        return $polygones;
    }
}
