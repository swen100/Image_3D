<?php
namespace Image3D;

require_once('../vendor/autoload.php');

$world = new Image_3D();
$world->setColor(new Color(255, 255, 255));

$light = $world->createLight('Light', array(0, 1000, 1000));
$light->setColor(new Color(255, 255, 255));

$pie = $world->createObject('pie', array('start' => 0, 'end' => 120, 'detail' => 20, 'outside' => 150));
$pie->setColor(new Color(0, 0, 255));

$pie = $world->createObject('pie', array('start' => 120, 'end' => 165, 'detail' => 20, 'outside' => 150));
$pie->setColor(new Color(255, 0, 0));

$pie = $world->createObject('pie', array('start' => 165, 'end' => 240, 'detail' => 20, 'outside' => 150));
$pie->setColor(new Color(255, 255, 0));

$pie = $world->createObject('pie', array('start' => 240, 'end' => 360, 'detail' => 20, 'outside' => 150));
$pie->setColor(new Color(0, 255, 0));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->transform($world->createMatrix('Scale', array(1, 1, 10)));
$world->transform($world->createMatrix('Rotation', array(-60, 0, 0)));

$world->createRenderer('perspectively');
$world->createDriver('ZBuffer');
$world->render(400, 400, 'Image_3D_Object_Pie.png');

#echo $world->stats( );

header("content-type: image/png");
readfile("Image_3D_Object_Pie.png");