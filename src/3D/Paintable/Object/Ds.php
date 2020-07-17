<?php

namespace Image3D\Paintable\Object;

use Image3D\Matrix;

/**
 * Image_3D_Object_3ds
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
class Ds extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var string
     */
    protected $_file;
    
    #protected $_fileSize;
    
    /**
     * @var array
     */
    protected $_objects = [];
    
    /**
     * @var Chunk
     */
    protected $_chunks;

    /**
     *
     * @param string $file
     * @throws \Exception
     */
    public function __construct($file)
    {
        parent::__construct();

        if (!is_file($file) || !is_readable($file)) {
            throw new \Exception("3ds file ($file) could not be loaded.");
        }
        $this->_file = $file;

        $this->readChunks();
    }

    protected function readChunks()
    {
        $this->_chunks = new Chunk(Chunk::MAIN3DS, substr(file_get_contents($this->_file), 6));
        $this->_chunks->readChunks();

        $editor = $this->_chunks->getFirstChunkByType(Chunk::EDIT3DS);
        $editor->readChunks();

        $objects = $editor->getChunksByType(Chunk::EDIT_OBJECT);
        foreach ($objects as $object) {
            $object = new ChunkObject($object->getType(), $object->getContent());
            $object->readChunks($this);
        }
    }

    public function addObject($id)
    {
        $id = (string) $id;
        $this->_objects[$id] = new DsObject();
        return $this->_objects[$id];
    }

    public function getObjectIDs()
    {
        return array_keys($this->_objects);
    }

    public function getObject($id)
    {
        if (!isset($this->_objects[$id])) {
            return false;
        }
        return $this->_objects[$id];
    }

    public function paint()
    {
        foreach ($this->_objects as $object) {
            $object->paint();
        }
    }

    public function getPolygonCount()
    {
        $count = 0;
        foreach ($this->_objects as $object) {
            $count += $object->getPolygonCount();
        }
        return $count;
    }

    public function setColor(\Image3D\Color $color)
    {
        foreach ($this->_objects as $object) {
            $object->setColor($color);
        }
    }

    public function setOption($option, $value)
    {
        foreach ($this->_objects as $object) {
            $object->setOption($option, $value);
        }
    }

    public function transform(Matrix $matrix, $id = null)
    {
        if ($id === null) {
            $id = substr(md5(microtime()), 0, 8);
        }
        foreach ($this->_objects as $object) {
            $object->transform($matrix, $id);
        }
    }

    public function subdivideSurfaces($factor = 1)
    {
        foreach ($this->_objects as $object) {
            $object->subdivideSurfaces($factor);
        }
    }

    public function getPolygones()
    {
        $polygones = array();
        foreach ($this->_objects as $object) {
            $polygones = array_merge($polygones, $object->getPolygones());
        }
        return $polygones;
    }
}
