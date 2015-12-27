<?php

class Test implements IteratorAggregate{
    
    public $customdata = array(1,2,3,4,5,6);
    
    public function getIterator(){
        return new ArrayIterator($this);    
    }
}

$test = new Test();
foreach($test as $li)
    echo $li;
