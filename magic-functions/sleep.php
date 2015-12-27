<?php
/*
*@ __sleep
*@description
* sleep function automaically called whenever perform  serialize / unserialize  of an object.
*  we cann't store object in session,cokkie,database. so using serialize we convert object into text/ string.
*	PDO object is not serialize;
*/
class ABC{
	public $database;
	public function __construct(){
		$this->database ="MySQL Connected";
	}
	public function __toSleep(){
		$this->database = "MySQL Disconnected";
	}
}

$obj = new ABC();
echo 'status '.$obj->database."\n";
echo $serialArray = serialize($obj)."\n";
echo 'status '.$obj->database;
