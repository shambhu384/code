<?php

class abc{
	public function __construct(){
		 echo 'abc constructor'."\n";
	}
}
abstract class pqr extends abc{

	public function fun(){
		echo 'fun of pqr';
	}
	public function __construct(){
	 echo 'pqr constructor'."\n";
	}
}
 class test extends pqr{
	public function __construct(){
	   echo 'test constructor'."\n";
	}
 }


$obj = new test();
$obj->fun();
var_dump($obj);