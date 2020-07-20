<?php
namespace Image3D;

require_once('../vendor/autoload.php');

$world = new Image_3D();
$world->setColor(new Color(240, 240, 240));

$light1 = $world->createLight('Light', [-300, 0, -300]);
$light1->setColor(new Color(100, 100, 255));

$light2 = $world->createLight('Light', [300, -300, -300]);
$light2->setColor(new Color(100, 255, 100));

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
            $matrix = new \Image3D\Matrix\Move([
                $x * ($size + $offset),
                $y * ($size + $offset),
                $z * ($size + $offset)]
            );
            $cube->transform($matrix);
        }
    }
}

$world->transform($world->createMatrix('Rotation', [220, 50, 0]));
$world->transform($world->createMatrix('Scale', [2, 2, 2]));

#$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, true);
#$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
#$world->createDriver('SVGControl');
$world->createDriver('GD');
$world->render(250, 250, 'Image_3D_Cubes.png');

#echo $world->stats();
header("content-type: image/png");
readfile("Image_3D_Cubes.png");