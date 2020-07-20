<?php

namespace Image3D;

require_once('../vendor/autoload.php');

$world = new Image_3D();
$world->setColor(new Color(0, 0, 0));

$light1 = $world->createLight('Light', array(-300, 0, -300));
$light1->setColor(new Color(252, 175, 62));

$light2 = $world->createLight('Light', array(300, -300, -300));
$light2->setColor(new Color(164, 0, 0));

$count = 3;

$size = 20;
$offset = 10;

for ($x = -($count - 1) / 2; $x <= ($count - 1) / 2; ++$x) {
    for ($y = -($count - 1) / 2; $y <= ($count - 1) / 2; ++$y) {
        for ($z = -($count - 1) / 2; $z <= ($count - 1) / 2; ++$z) {
//        	if (max(abs($x), abs($y), abs($z)) < ($count - 1) / 2) continue;
            if (max($x, $y, $z) <= 0) {
                continue;
            }

            $cube = $world->createObject('Quadcube', [$size, $size, $size]);
            $cube->setColor(new Color(255, 255, 255, 75));
            $cube->transform($world->createMatrix('Move', [$x * ($size + $offset), $y * ($size + $offset), $z * ($size + $offset)]));
        }
    }
}

$world->transform($world->createMatrix('Rotation', [220, 50, 0]));
$world->transform($world->createMatrix('Scale', [2, 2, 2]));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, true);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
$world->createDriver('DynamicCanvas');
$world->render(250, 250, 'Image_3D_Dynamic_Cubes.js');

#echo $world->stats();

#header("content-type: image/png");
#readfile("Image_3D_Dynamic_Cubes.png");
