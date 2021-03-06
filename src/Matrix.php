<?php

namespace Image3D;

/**
 * @category  Image
 * @package   Image_3D
 * @author    Kore Nordmann <3d@kore-nordmann.de>
 * @copyright 1997-2005 Kore Nordmann
 * @link      http://pear.php.net/package/Image_3D
 */
class Matrix
{
    /**
     * @var array
     */
    protected $_matrix = [];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setUnitMatrix();
    }

    /**
     *
     * @param int $x
     * @param int $y
     * @param number $value
     * @return bool
     */
    public function setValue($x, $y, $value)
    {
        if (!isset($this->_matrix[$x][$y])) {
            return false;
        }
        
        $this->_matrix[$x][$y] = (float) $value;
        
        return true;
    }

    /**
     *
     * @param int $x
     * @param int $y
     * @return false|float
     */
    public function getValue($x, $y)
    {
        if (!isset($this->_matrix[$x][$y])) {
            return false;
        }
        
        return (float) $this->_matrix[$x][$y];
    }

    /**
     * @return void
     */
    public function setUnitMatrix()
    {
        $this->_matrix = [
            [1., 0., 0., 0.],
            [0., 1., 0., 0.],
            [0., 0., 1., 0.],
            [0., 0., 0., 1.]
        ];
    }

    /**
     * @param \Image3D\Matrix $matrix
     * @return \Image3D\Matrix
     */
    public function diff(Matrix $matrix): Matrix
    {
        $result = new Matrix();
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $result->setValue($i, $j, $matrix->getValue($i, $j) - $this->getValue($i, $j));
            }
        }
        return $result;
    }

    /**
     * @param number $skalar
     * @return void
     */
    public function multiplySkalar($skalar)
    {
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $this->_matrix[$i][$j] *= $skalar;
            }
        }
    }

    /**
     * @return void
     */
    public function dump()
    {
        foreach ($this->_matrix as $row) {
            printf("|\t%2.2f\t%2.2f\t%2.2f\t%2.2f\t|\n", $row[0], $row[1], $row[2], $row[3]);
        }
    }

    /**
     * @param number $rotationX
     * @param number $rotationY
     * @param number $rotationZ
     * @return void
     */
    public function setRotationMatrix($rotationX, $rotationY, $rotationZ)
    {
        $rotationY = (float) $rotationY;
        $rotationZ = (float) $rotationZ;

        if (!empty($rotationX)) {
            // Normalisierung der Rotation von Grad auf radiale Berechnung
            $rotationX = deg2rad((float) $rotationX);
            // Setzen der Rotationsmatrix fuer Drehungen an der X-Achse
            $matrix = new Matrix();

            $matrix->setValue(1, 1, cos($rotationX));
            $matrix->setValue(1, 2, sin($rotationX));
            $matrix->setValue(2, 1, -sin($rotationX));
            $matrix->setValue(2, 2, cos($rotationX));

            // Setzen der Transformationsmatrix
            $this->multiply($matrix);
            unset($matrix);
        }

        if (!empty($rotationY)) {
            // Normalisierung der Rotation von Grad auf radiale Berechnung
            $rotationY = deg2rad((float) $rotationY);
            // Setzen der Rotationsmatrix fuer Drehungen an der X-Achse
            $matrix = new Matrix();

            $matrix->setValue(0, 0, cos($rotationY));
            $matrix->setValue(0, 2, -sin($rotationY));
            $matrix->setValue(2, 0, sin($rotationY));
            $matrix->setValue(2, 2, cos($rotationY));

            // Setzen der Transformationsmatrix
            $this->multiply($matrix);
            unset($matrix);
        }

        if (!empty($rotationZ)) {
            // Normalisierung der Rotation von Grad auf radiale Berechnung
            $rotationZ = deg2rad((float) $rotationZ);
            // Setzen der Rotationsmatrix fuer Drehungen an der X-Achse
            $matrix = new Matrix();

            $matrix->setValue(0, 0, cos($rotationZ));
            $matrix->setValue(0, 1, sin($rotationZ));
            $matrix->setValue(1, 0, -sin($rotationZ));
            $matrix->setValue(1, 1, cos($rotationZ));

            // Setzen der Transformationsmatrix
            $this->multiply($matrix);
            unset($matrix);
        }
    }

    /**
     * @param number $moveX
     * @param number $moveY
     * @param number $moveZ
     * @return void
     */
    public function setMoveMatrix($moveX, $moveY, $moveZ)
    {
        $matrix = new Matrix();
        $matrix->setValue(3, 0, (float) $moveX);
        $matrix->setValue(3, 1, (float) $moveY);
        $matrix->setValue(3, 2, (float) $moveZ);

        $this->multiply($matrix);
    }

    /**
     * @param number $scaleX
     * @param number $scaleY
     * @param number $scaleZ
     * @return void
     */
    public function setScaleMatrix($scaleX, $scaleY, $scaleZ)
    {
        $matrix = new Matrix();
        $matrix->setValue(0, 0, (float) $scaleX);
        $matrix->setValue(1, 1, (float) $scaleY);
        $matrix->setValue(2, 2, (float) $scaleZ);

        $this->multiply($matrix);
    }

    /**
     * @param \Image3D\Matrix $matrix
     * @return $this
     */
    public function multiply(Matrix $matrix)
    {
        $new = clone($this);

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $sum = 0;
                for ($k = 0; $k < 4; $k++) {
                    $sum += $new->getValue($i, $k) * $matrix->getValue($k, $j);
                }
                $this->setValue($i, $j, $sum);
            }
        }

        return $this;
    }
}
