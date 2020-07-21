<?php

namespace Image3D\Paintable\Object;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ChunkObject extends Chunk
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param number $type
     * @param string $content
     */
    public function __construct($type, string $content)
    {
        parent::__construct($type, $content);
        $this->createName();
    }

    /**
     * @return void
     */
    protected function createName()
    {
        $i = 0;
        $this->name = '';
        while ((ord($this->content{$i}) !== 0) && ($i < $this->size)) {
            $this->name .= $this->content{$i++};
        }
        
        $this->content = substr($this->content, $i + 1);
    }

    /**
     * @param \Image3D\Paintable\Object\Ds $k3ds
     * @return bool
     */
    public function readChunks(Ds $k3ds = null): bool
    {
        if (strlen($this->content) < 6) {
            return false;
        }
        
        $subtype = $this->getWord(substr($this->content, 0, 2));
        $subcontent = substr($this->content, 6);

        switch ($subtype) {
            case self::OBJ_TRIMESH:
                $object = $k3ds->addObject($this->name);
                $this->chunks[] = new ChunkTriMesh($subtype, $subcontent, $object);
                break;
        }
        
        return true;
    }

    public function debug()
    {
        echo 'Object: ', $this->name, "\n";
        parent::debug();
    }
}
