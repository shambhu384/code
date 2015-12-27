<?php
/*
	Magic funtions
	@ __get 
	@ description
	# this function is automatically called when we are trying to access
	non-existing variable / private variable.
	Using we can acces the variable but cannot manuplate{write}.
	# __get is utilized for reading data from inaccessible properties.
/*----------------------------*/
class ABC{

	private $array = ['abc'=>'ABC variable','xyz'=>'XYZ variable'];
	private $list=4;

	/* in get function we recive name of the variable that you are looking */
	/* that many time of function call when we didn't get variable */
	public function __get($name){
		if(array_key_exists($name,$this->array)){
			return $array[$name];
		/* check property is available */	
		}else if(property_exists($this, $name)){
		    return $this->$name;
		}else{
			return "Trying to access not existing variable : $name<br/>";
		}
	}
}

$obj = new ABC;
/* Fatal error: Cannot access private property ABC::$array */
echo $obj->list;
/* Notice: Undefined property: ABC::$name in */
echo $obj->name;
/* @solutions
 we create a __get() function who
 get automatically called when variable not found / cannot access.
 */
