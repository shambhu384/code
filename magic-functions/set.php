<?php
/*
	Magic funtions
	@ __set 
	@ description
	# this function is automatically called when we are trying to writing
	non-existing variable / private variable.
	Using we can access the variable and can manuplate{write}.
	# __set is run when writing data to inaccessible properties.
/*----------------------------*/

class ABC{

	private $array = array();
	private $key;
	// public $parray =array(); can read/write

	/* in get function we recive name of the variable that you are looking */
	/* that many time of function call when we didn't get variable */
	public function __set($name,$value){
		if(!array_key_exists($name,$this->array)){
			return $this->array[$name]=$value;
		}else if(property_exists($this, $name)) {
          	  $this->$name = $value;
		}else{
			return "Cann't set value of in array()";
		}
	}

	public function __get($name){
		if(property_exists($this, $name)) {
          	return $this->$name;
        }  	  
	}

	public function results(){
		return $this->array;
	}
}
/* create object */
$obj = new ABC;
/* set the value/ assign value 
$obj->parray =array('asd','xyz');
var_dump($obj->parray);
*/
$obj->text = 'abc';
$obj->key="!@#$%^&";
echo $obj->key;
var_dump($obj->results());
//var_dump($obj->array);


/* reference */
/*
@this is slower (than getters/setters)
@there is no auto-completion (and this is a major problem actually), and type management by the IDE for refactoring and code-browsing (under Zend Studio/PhpStorm this can be handled with the @property phpdoc annotation but that requires to maintain them: quite a pain)
@the documentation (phpdoc) doesn't match how your code is supposed to be used, and looking at your class doesn't bring much answers as well. This is confusing.
@added after edit: having getters for properties is more consistent with "real" methods where getXXX() is not only returning a private property but doing real logic. You have the same naming. For example you have $user->getName() (returns private property) and $user->getToken($key) (computed). The day your getter gets more than a getter and needs to do some logic, everything is still consistent.
*/

/* other option */
/*
class MyClass {

    private $firstField;
    private $secondField;

    public function getFirstField() {
        return $this->firstField;
    }

    public function setFirstField($firstField) {
        $this->firstField = $firstField;
    }

    public function getSecondField() {
        return $this->secondField;
    }

    public function setSecondField($secondField) {
        $this->secondField = $secondField;
    }

}

$myClass = new MyClass();

$myClass->setFirstField("This is a foo line");
$myClass->setSecondField("This is a bar line");

echo $myClass->getFirstField();
echo $myClass->getSecondField();

*/