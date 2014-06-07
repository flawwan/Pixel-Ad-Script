<?php
include 'class/cords.php';


$c = new Cords(500,500);
$c->cords(40, 40, 'img/test.png');

$c->render();