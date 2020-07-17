<?php
namespace Image3D;

require_once('../src/autoload.php');
require_once('../src/3D.php');

$world = new Image_3D();
$world->setColor(new Color(255, 255, 255));

$light = $world->createLight('Light', array(-2000, -2000, -2000));
$light->setColor(new Color(0, 0, 255));

$cone = $world->createObject('Cone', array('detail' => 1));
$cone->setColor(new Color(255, 255, 255, 200));

$cone->transform($world->createMatrix('Scale', array(100, 400, 100)));
$cone->transform(
  $world->createMatrix('Move', array(0, -80, 0))->multiply(
  $world->createMatrix('Rotation', array(150, 30, 30))
  )
);

$world->createRenderer('perspectively');
$world->createDriver('GD');
$world->render(400, 400, 'Image_3D_Object_Cone.png');

#echo $world->stats( );

header("content-type: image/png");
readfile("Image_3D_Object_Cone.png");