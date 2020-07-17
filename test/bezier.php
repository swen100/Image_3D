<?php

namespace Image3D;

require_once('../src/autoload.php');
require_once('../src/3D.php');

$world = new Image_3D();
$world->setColor(new Color(80, 80, 80));

$light = $world->createLight('Light', array(-1000, -1000, -1000));
$light->setColor(new Color(255, 255, 255));

$redSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(0, -25, 0), 'angle' => 30, 'float' => 2));
$redSpot->setColor(new Color(255, 0, 0));

$blueSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(-35, 25, 0), 'angle' => 30, 'float' => 2));
$blueSpot->setColor(new Color(0, 0, 255));

$greenSpot = $world->createLight('Spotlight', array(0, 0, -200, 'aim' => array(35, 25, 0), 'angle' => 30, 'float' => 2));
$greenSpot->setColor(new Color(0, 255, 0));

$bezier = $world->createObject('bezier',
    array( 'x_detail' => 120, 
            'y_detail' => 120,
            'points' => array(
        array(  array(200, -150, -200),
                array(-100, 150, 600),
                array(-300, 150, -600),
                array(200, -150, 200),
            ),
        array(  array(0, -200, -100),
                array(0, 100, 250),
                array(0, 200, -250),
                array(0, -100, 100),
            ),
        array(  array(-150, -150, -200),
                array(200, 150, 300),
                array(200, 200, -300),
                array(-150, -150, 200),
            ),
    )));
$bezier->setColor(new Color(250, 250, 250));
$bezier->transform($world->createMatrix('Rotation', array(0, 120, 180)));

$renderer = $world->createRenderer('perspectively');

$world->createDriver('ZBuffer');
$world->render(400, 400, 'Image_3D_Object_Bezier.png');

#echo $world->stats();

header("content-type: image/png");
readfile("Image_3D_Object_Bezier.png");