<?php

class A{

	public static $name;
	public static $address;

	public $pincode;

	public function getName(){
		return self::$name;
	}

	public static function test(){
		return "static called from A";
	}
}
class B extends A{
	public static $pin;
}
echo B::$name = 'scott';
echo   B::test();
$obj = new A();
//echo $obj->getName();
//$obj->address;
