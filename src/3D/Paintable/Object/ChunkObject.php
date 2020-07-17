<?php

namespace Image3D\Paintable\Object;

class /*Image_3D_*/ChunkObject extends /*\Image3D\Image_3D_*/ Chunk
{

    protected $name;

    public function __construct($type, $content)
    {
        parent::__construct($type, $content);
        $this->getName();
    }

    protected function getName()
    {
        $i = 0;
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
