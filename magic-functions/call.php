<?php
/*
	Magic funtions
	@ __call 
	@ description
	# In order to call object of member function.
	Using we can access the functions.
	# __call() is triggered when invoking inaccessible methods in an object context.
/*----------------------------*/


class ABC{

	private $array = ['username'=>'scott','password'=>'p@$$word','isActive'=>true];
	
	private $user = 'scott';
	public function getUser(){
		return $this->user;
	}

	/* create __call function */
	/* name is string & parameter is array*/
	public function __call($function_name,$function_parameter){
		 return "$function_name".implode(', ', $function_parameter). "\n";
		/*if(array_key_exists($function_name,$this->array)){
			return $this->array[$function_name];
		}else{
			echo "You are trying to call a function name $function_name with following parameter:";			
			print_r($function_parameter);
		}*/		
	}

	private function getUserName($var){
		return 'sdfs'.$var;
	}

}

$obj =  new ABC();
/* call member function. */
//echo $obj->getUser();

/* Fatal error: Call to undefined method ABC::getUserName() */
echo $obj->getUserName('abc');

//echo $obj->isActive()?'Logged In':'Not Logged In';

