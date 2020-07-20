<?php

namespace Image3D\Driver;

use Image3D\Color;
use Image3D\Point;
use Image3D\Paintable\Polygon;
use Image3D\Renderer;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class ASCII extends \Image3D\Driver
{

    /**
     * @var float
     */
    public static $IMAGE_3D_DRIVER_ASCII_GRAY = 0.01;

    /**
     * @var array
     */
    protected $_size = [0, 0];
    
    /**
     * @var array
     */
    protected $_points = [];
    
    /**
     * @var array
     */
    protected $_heigth = [];
    
    /**
     * @var array
     */
    protected $_charArray = [
        0 => ' ',
        1 => '`',
        2 => '\'',
        3 => '^',
        4 => '-',
        5 => '`',
        6 => '/',
        7 => '/',
        8 => '-',
        9 => '\\',
        10 => '\'',
        11 => '\\',
        12 => '~',
        13 => '+',
        14 => '+',
        15 => '*',
        16 => '.',
        17 => '|',
        18 => '/',
        19 => '/',
        20 => '|',
        21 => '|',
        22 => '/',
        23 => '/',
        24 => '/',
        25 => ')',
        26 => '/',
        27 => 'Y',
        28 => 'r',
        29 => '}',
        30 => '/',
        31 => 'P',
        32 => '.',
        33 => '\\',
        34 => '|',
        35 => '^',
        36 => '\\',
        37 => '\\',
        38 => '(',
        39 => '(',
        40 => ':',
        41 => '\\',
        42 => '|',
        43 => 'I',
        44 => ';',
        45 => '\\',
        46 => '{',
        47 => '9',
        48 => '_',
        49 => '_',
        50 => '_',
        51 => 'C',
        52 => '<',
        53 => 'L',
        54 => 'l',
        55 => 'C',
        56 => '>',
        57 => 'J',
        58 => 'J',
        59 => 'J',
        60 => 'o',
        61 => 'b',
        62 => 'd',
        63 => '#',
    ];

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->_points = [];
        $this->_heigth = [];
        $this->_image = [];
    }

    /**
     * Create the inital image
     *
     * @param number $x width of the image
     * @param number $y height of the image
     *
     * @return void
     */
    public function createImage($x, $y)
    {
        $this->_size = [$x, $y];
    }

    /**
     * @param Color $colorObj
     * @param float $alpha default 1.0
     * @return array
     */
    protected function getColor(Color $colorObj, $alpha = 1.): array
    {
        $values = $colorObj->getValues();
        return [$values[0], $values[1], $values[2], (1 - $values[3]) * $alpha];
    }

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    protected function mixColor($old, $new): array
    {
        $faktor = (1 - $new[3]) * $old[3];
        
        return array(
            $old[0] * $faktor + $new[0] * $new[3],
            $old[1] * $faktor + $new[1] * $new[3],
            $old[2] * $faktor + $new[2] * $new[3],
            $old[3] * $old[3] + $new[3]
        );
    }

    /**
     * @param Color $color
     * @return void
     */
    public function setBackground(Color $color)
    {
        $bg = $this->getColor($color);

        for ($x = 0; $x < $this->_size[0]; ++$x) {
            for ($y = 0; $y < $this->_size[1]; ++$y) {
                $this->_image[$x][$y] = $bg;
            }
        }
    }

    /**
     * @param Point $p1
     * @param Point $p2
     * @return array
     */
    protected function drawLine(Point $p1, Point $p2): array
    {
        list($x1, $y1) = $p1->getScreenCoordinates();
        list($x2, $y2) = $p2->getScreenCoordinates();

        $steps = ceil(max(abs($x1 - $x2), abs($y1 - $y2)));

        $xdiff = ($x2 - $x1) / $steps;
        $ydiff = ($y2 - $y1) / $steps;

        $points = [];
        for ($i = 0; $i < $steps; ++$i) {
            $points[(int) round($x1 + $i * $xdiff)][(int) round($y1 + $i * $ydiff)] = true;
        }
        
        return $points;
    }

    /**
     * @param array $pointArray
     * @return array
     */
    protected function getPolygonOutlines(array $pointArray): array
    {
        $map = [];

        $last = end($pointArray);
        foreach ($pointArray as $point) {
            $line = $this->drawLine($last, $point);
            $last = $point;
            // Merge line to map
            foreach ($line as $x => $row) {
                foreach ($row as $y => $height) {
                    $map[(int) $x][(int) $y] = $height;
                }
            }
        }

        return $map;
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawPolygon(Polygon $polygon)
    {
        $points = $this->getPolygonOutlines($polygon->getPoints());

        foreach ($points as $x => $row) {
            if (count($row) < 2) {
                continue;
            }

            $start = min(array_keys($row));
            $end = max(array_keys($row));

            // Starting point
            $this->_heigth[$x][$start] = $this->getColor($polygon->getColor());

            // the way between
            for ($y = $start + 1; $y < $end; ++$y) {
                $this->_heigth[$x][$y] = $this->getColor($polygon->getColor());
            }

            // Ending point
            $this->_points[$x][$end] = $this->getColor($polygon->getColor());
        }
    }

    /**
     * @param Polygon $polygon
     * @return void
     */
    public function drawGradientPolygon(Polygon $polygon)
    {
        $this->drawPolygon($polygon);
    }

    /**
     *
     * @param array $color
     * @param string $last
     * @return string
     */
    public function getAnsiColorCode($color, $last = '')
    {
        $code = "\033[0;" . (30 + bindec((int) round($color[2]) . (int) round($color[1]) . (int) round($color[0]))) . 'm';
        if ($last !== $code) {
            return $code;
        }
        return '';
    }

    /**
     *
     * @param string $file
     * @return bool
     */
    public function save(string $file): bool
    {
        $asciiWidth = (int) ceil($this->_size[0] / 2);
        $asciiHeight = (int) ceil($this->_size[1] / 6);

        $output = "\033[2J";
        $lastColor = '';

        for ($y = 0; $y < $asciiHeight; ++$y) {
            for ($x = 0; $x < $asciiWidth; ++$x) {
                // Get pixelarray
                $char = 0;

                $charColor = array(0, 0, 0);
                for ($xi = 0; $xi < 2; ++$xi) {
                    for ($yi = 0; $yi < 3; ++$yi) {
                        $xPos = $x * 2 + $xi;
                        $yPos = $y * 6 + $yi;

                        if (isset($this->_heigth[$xPos][$yPos])) {
                            $color = $this->mixColor($this->_image[$xPos][$yPos], $this->_heigth[$xPos][$yPos]);
                            if ((($color[0] + $color[1] + $color[2]) / 3) > self::$IMAGE_3D_DRIVER_ASCII_GRAY) {
                                $char |= pow(2, $yi + ($xi * 3));
                            }
                            $charColor[0] += $color[0];
                            $charColor[1] += $color[1];
                            $charColor[2] += $color[2];
                        }
                    }
                }
                $lastColor = $this->getAnsiColorCode([$charColor[0] / 6, $charColor[1] / 6, $charColor[2] / 6], $lastColor);
                $output .= $lastColor . $this->_charArray[$char];
            }
            $lastColor = '';
            $output .= "\n";
        }
        $fp = fopen($file, 'w');
        $success = fwrite($fp, $output);
        fclose($fp);
        
        return $success !== false;
    }

    /**
     *
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
