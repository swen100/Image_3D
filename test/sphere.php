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

$sphere = $world->createObject('sphere', array('r' => 150, 'detail' => 4));
$sphere->setColor(new Color(150, 150, 150));

$renderer = $world->createRenderer('perspectively');

$world->createDriver('GD');
$world->render(400, 400, 'Image_3D_Object_Sphere.png');

#echo $world->stats();

header("content-type: image/png");
readfile("Image_3D_Object_Sphere.png");