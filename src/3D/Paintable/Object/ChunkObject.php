<?php

namespace Image3D\Paintable\Object;

class ChunkObject extends Chunk
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     *
     * @param number $type
     * @param string $content
     */
    public function __construct($type, $content)
    {
        parent::__construct($type, $content);
        $this->createName();
    }

    protected function createName()
    {
        $i = 0;
        $this->name = '';
        while ((ord($this->content{$i}) !== 0) && ($i < $this->size)) {
            $this->name .= $this->content{$i++};
        }
        $this->content = substr($this->content, $i + 1);
    }

    public function readChunks(Ds $k3ds = null)
    {
        $subtype = $this->getWord(substr($this->content, 0, 2));
        $subcontent = substr($this->content, 6);

        switch ($subtype) {
            case self::OBJ_TRIMESH:
                $object = $k3ds->addObject($this->name);
                $this->chunks[] = new ChunkTriMesh($subtype, $subcontent, $object);
                break;
        }
    }

    public function debug()
    {
        echo 'Object: ', $this->name, "\n";
        parent::debug();
    }
}
