<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

class ImageMagick extends \Image3D\Driver
{

    /**
     * Array of parameter strings passed to 'convert' binary.
     *
     * @var array
     * @access protected
     */
    protected $_commandQueue = [];
    
    /**
     * @var array
     */
    private $_dimensions = [];
    
    /**
     * @var string
     */
    private $_filetype;

    public function __construct()
    {
    }

    public function createImage($x, $y)
    {
        $this->_image = tempnam(sys_get_temp_dir(), 'IMG');

        $this->_dimensions = ['x' => $x, 'y' => $y];

        $this->_commandQueue[] = "-size {$x}x{$y} xc:transparent";
    }

    protected function _getColor(Color $color)
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
        $colorString = $this->_getColor($color);
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

        $command = '-fill ' . escapeshellarg($this->_getColor($polygon->getColor()));
        $command .= ' -draw ' . escapeshellarg('polygon ' . trim($coords));

        $this->_commandQueue[] = $command;
    }

    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    public function setFiletye($type)
    {
        $type = strtolower($type);
        if (in_array($type, ['png', 'jpeg'])) {
            $this->_filetype = $type;
            return true;
        } else {
            return false;
        }
    }

    public function save($file): bool
    {
        $command = '';
        #$commandCount = 1;
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

    public function getSupportedShading(): array
    {
        return [
            Renderer::SHADE_NO,
            Renderer::SHADE_FLAT
        ];
    }
}
