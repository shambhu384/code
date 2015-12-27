<?php


class Test implements Iterator{
    
    private $id;
    private $name;
    protected $address = 'bangalore';
    public $userdata = 'private files';
    public $numdata = 'private number';
    public $text = 'Text';

    public $customdata = array(1,2,3,4,5,6);

    public function rewind() {
        reset($this->customdata);
    }
        
    public function current() {
        return current($this->customdata);
    }
    
    public function key() {
        return key($this->customdata);
    }

    public function next(){
        return next($this->customdata);    
    }

    public function valid(){
        $key = key($this->customdata);
        return ($key !== NULL && $key !== FALSE);
    }
}

$test = new Test();

while($test->valid()){
    echo $test->current();
    $test->next();
    //$test->rewind();
}
