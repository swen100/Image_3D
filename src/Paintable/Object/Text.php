<?php

namespace Image3D\Paintable\Object;

use Image3D\Matrix\Move;

/**
 * Image_3D_Object_Text
 *
 * @category   Image
 * @package    Image_3D
 * @author     Kore Nordmann <3d@kore-nordmann.de>
 * @copyright  1997-2005 Kore Nordmann
 * @license    http://www.gnu.org/licenses/lgpl.txt lgpl 2.1
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_3D
 * @since      Class available since Release 0.1.0
 */
class Text extends \Image3D\Paintable\Base3DObject
{

    /**
     * @var string
     */
    protected $_text = '';
    
    /**
     * @var float
     */
    protected $_characterSpacing;
    
    /**
     * @var array
     */
    protected $_chars = [];

    /**
     *
     * @param string $string
     * @throws \Exception
     * @return void
     */
    public function __construct($string)
    {
        $this->_text = (string) $string;
        $this->_characterSpacing = 5.5;

        $textdata = '../../data/TextData.dat';
        if (is_readable($textdata)) {
            $this->_chars = unserialize(file_get_contents($textdata));
        } elseif (is_readable('data/TextData.dat')) {
            $this->_chars = unserialize(file_get_contents('data/TextData.dat'));
        } else {
            throw new \Exception('File for textdata not found.');
        }

        $this->generateCubes();
    }

    /**
     *
     * @param number $charSpacing
     * @return void
     */
    public function setCharSpacing($charSpacing)
    {
        $this->_characterSpacing = 5 + (float) $charSpacing;
    }

    /**
     * @return void
     */
    protected function generateCubes()
    {
        $length = strlen($this->_text);

        for ($i = 0; $i < $length; ++$i) {
            $char = $this->_chars[ord($this->_text{$i})];
            foreach ($char as $x => $row) {
                foreach ($row as $y => $pixel) {
                    //printf("Dot %d %.1f %.1f\n", $pixel, $x + $i * $this->_characterSpacing, $y);
                    if (!$pixel) {
                        continue;
                    }
                    
                    $tmp = new Cube(array(1, 1, 1));
                    $tmp->transform(new Move(array($x + $i * $this->_characterSpacing, $y, 0)));
                    $polygones = $tmp->getPolygones();
                    foreach ($polygones as $polygon) {
                        $this->addPolygon($polygon);
                    }
                    unset($tmp);
                }
            }
        }
    }
}
