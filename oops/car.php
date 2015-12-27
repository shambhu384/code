<?php 
error_reporting(E_ALL);

include_once 'engine.php';

class Car{
	
	public $engine ;

	public function createEngine(){
	   $this->engine =new Engine;	
	}

	public function getCarName(){
		return "Audi R8";
	}
}

$car = new Car();
$car->createEngine();
echo $car->engine->getModelName();
///ho cho /echo '<pre>'.print_r($car->createEngine()->getModelName());