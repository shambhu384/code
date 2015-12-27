<?php
/*
	@ __unset()
	@ description
	# __unset() is invoked when unset() is used on inaccessible properties.
*/
/*
	@ __unset() is automatically called whenver we unset the variable. 
	@ It is a language construct that checks the initialization of variables or class properties
*/

class ABC{

	public $array = ['abc'=>'ABC variable','xyz'=>'XYZ variable'];
	
	public function __isset($name){
        echo "Is '$name' set?\n";
        return isset($this->array[$name]);
    }

     /**  As of PHP 5.1.0  */
    public function __unset($name)
    {
        echo "\nUnsetting '$name'\n";
        unset($this->array[$name]);
    }
	
	public function __get($name){
		if(array_key_exists($name,$this->array)){
			return $this->array[$name];
		}else{
			return 'Trying to access non existing property';
		}
	}
}

$obj = new ABC();

echo $obj->abc;
unset($obj->abc);
echo $obj->abc;
