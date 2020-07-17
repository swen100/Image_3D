<?php
namespace Image3D;

require_once('../src/autoload.php');
require_once('../src/3D.php');

$world = new Image_3D();
$world->setColor(new Color(255, 255, 255));

$light = $world->createLight('Light', array(-2000, -2000, -2000));
$light->setColor(new Color(255, 255, 255));

$redLight = $world->createLight('Light', array(90, 0, 50));
$redLight->setColor(new Color(255, 0, 0));

$torus = $world->createObject('torus', array('inner_radius' => 110, 'outer_radius' => 170, 'detail_1' => 60, 'detail_2' => 30));
$torus->setColor(new Color(150, 150, 150));
$torus->transform($world->createMatrix('Rotation', array(60, -10, 0)));

$renderer = $world->createRenderer('perspectively');

$world->createDriver('GD');
$world->render(500, 500, 'Image_3D_Object_Torus.png');

#echo $world->stats();

header("content-type: image/png");
readfile("Image_3D_Object_Torus.png");