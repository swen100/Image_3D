<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ImageMagick extends \Image3D\Driver
{

    /**
     * @var array Array of parameter strings passed to 'convert' binary.
     */
    protected $_commandQueue = [];
    
    /**
     * @var array
     * @access private
     */
    private $_dimensions = [];
    
    /**
     * @var string
     */
    protected $_filetype = 'png';

    /**
     * @param number $x width of the image
     * @param number $y height of the image
     */
    public function createImage($x, $y)
    {
        $this->_image = tempnam(sys_get_temp_dir(), 'IMG');
        $this->_dimensions = ['x' => (int) $x, 'y' => (int) $y];
        $this->_commandQueue[] = "-size {$x}x{$y} xc:transparent";
    }

    /**
     * @param Color $color
     * @return string
     */
    protected function getColor(Color $color): string
    {
        $values = $color->getValues();

        $values[0] = (int) round($values[0] * 255);
        $values[1] = (int) round($values[1] * 255);
        $values[2] = (int) round($values[2] * 255);
        $values[3] = (int) round($values[3] * 127);

        $color = '';
        if ($values[3] > 0) {
            $color = 'rgba(' . implode(',', $values) . ')';
        } else {
            unset($values[3]);
            $color = 'rgb(' . implode(',', $values) . ')';
        }

        return $color;
    }

    public function setBackground(Color $color)
    {
        $colorString = $this->getColor($color);
        array_splice($this->_commandQueue, 1, 0, '-fill ' . escapeshellarg($colorString) . ' ' .
                '-draw ' . escapeshellarg('rectangle 0,0 ' . implode(',', $this->_dimensions)));
    }

    public function drawPolygon(Polygon $polygon)
    {
        // Get points
        $points = $polygon->getPoints();
        $coords = array();

        $coords = '';
        foreach ($points as $point) {
            $pointCoords = $point->getScreenCoordinates();

            $coords .= (int) $pointCoords[0] . ',' . (int) $pointCoords[1] . ' ';
        }

        $command = '-fill ' . escapeshellarg($this->getColor($polygon->getColor()));
        $command .= ' -draw ' . escapeshellarg('polygon ' . trim($coords));

        $this->_commandQueue[] = $command;
    }

    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    /**
     * @param string $type
     * @return boolean
     */
    public function setFiletye(string $type): bool
    {
        $type = strtolower($type);
        if (in_array($type, ['png', 'jpeg'])) {
            $this->_filetype = $type;
            return true;
        }
        
        return false;
    }

    /**
     * @param string $file Path to the file where to write the data.
     * @return bool
     */
    public function save(string $file): bool
    {
        $command = '';
        $firstRun = true;

        for ($i = 0; $i < count($this->_commandQueue); $i++) {
            $command .= ' ' . $this->_commandQueue[$i] . ' ';
            if (strlen($command) > 1000 || $i == count($this->_commandQueue) - 1) {
                $firstRun === false ? $command = $file . ' ' . $command : $firstRun = false;

                $command = 'convert ' . $command . ' ' . $file;
                // echo "Excuting command run <".$commandCount++.">\n";
                shell_exec($command);
                $command = '';
            }
        }
        
        return shell_exec($command) !== null;
    }

    /**
     * @return array
     */
    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO,
            Renderer::SHADE_FLAT
        ];
    }
}
