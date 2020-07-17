<?php
namespace Image3D;

require_once('../src/autoload.php');
require_once('../src/3D.php');

$world = new Image_3D();
$world->setColor(new Color(255, 255, 255));

$light = $world->createLight('Light', array(-2000, -2000, -2000));
$light->setColor(new Color(255, 255, 255));

$redSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(0, -25, 0), 'angle' => 30, 'float' => 2));
$redSpot->setColor(new Color(255, 0, 0));

$blueSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(-35, 25, 0), 'angle' => 30, 'float' => 2));
$blueSpot->setColor(new Color(0, 0, 255));

$greenSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(35, 25, 0), 'angle' => 30, 'float' => 2));
$greenSpot->setColor(new Color(0, 255, 0));

$map = $world->createObject('map');

$detail = 80;
$size = 200;
$height = 40;

$raster = 1 / $detail;
for ($x = -1; $x <= 1; $x += $raster) {
	$row = array();
	for ($y = -1; $y <= 1; $y += $raster) {
		$row[] = new Point($x * $size, $y * $size, sin($x * pi()) * sin($y * 2 * pi()) * $height);
	}
	$map->addRow($row);
}

$map->setColor(new Color(150, 150, 150, 0));

$world->transform($world->createMatrix('Rotation', array(-20, 10, -10)));

$world->createRenderer('perspectively');
$world->createDriver('GD');
$world->render(400, 400, 'Image_3D_Spotlights.png');

#echo $world->stats();

header("content-type: image/png");
readfile("Image_3D_Spotlights.png");