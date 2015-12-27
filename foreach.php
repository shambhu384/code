<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$std = new stdClass();
$std->name = 'hello';
$std->address = 'bangalore';

foreach ($std as $key => $value) {
    echo $value."\n";
}

if($std instanceof Traversable)
    echo 'implenfj';



class test implements IteratorAggregate{
    public function getIterator() {
        
    }

}
$test = new test();
if($test instanceof Traversable)
echo 'implenfj';