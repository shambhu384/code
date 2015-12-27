<?php
class ArrayAccessObject implements ArrayAccess, IteratorAggregate {

    public $data = array();
    private $color = array(1,2,3,4);

    public function __construct() {
        $this->data = array(
                            'one' => 1,
                            'two' => 2,
                            'three' => 3                             
                           );
    }
    public function offsetSet($offset, $value){
        if(is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
       return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function getIterator() {
        return new ArrayIterator($this);  
    }
}

$obj = new ArrayAccessObject();

var_dump($obj);
/*
foreach($obj as $val) {
    var_dump($val);
 }
*/
