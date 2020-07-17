<?php

namespace Image3D;

require_once('../src/autoload.php');
require_once('../src/3D.php');

$world = new Image_3D();
$world->setColor(new Color(250, 250, 250));

$light = $world->createLight('Point', [0, -200, 0, 'distance' => 300, 'falloff' => 2]);
$light->setColor(new Color(150, 150, 255));

$steps = 10;
$step = 20;

for ($i = 0; $i < $steps; ++$i) {
    $y = ($steps * $step / -2) + $i * $step;
    $p = $world->createObject('Polygon', [new Point(-100, $y, -30), new Point(-100, $y, 50), new Point(100, $y, 40)]);
    $p->setColor(new Color(255, 255, 255));
}

$world->transform($world->createMatrix('Rotation', [20, 0, 0]));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
$world->createDriver('ZBuffer');
$world->render(400, 400, 'Image_3D_Pointlight.png');

#echo $world->stats( );

header("content-type: image/png");
readfile("Image_3D_Pointlight.png");
