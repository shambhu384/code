<?php

$interfaces = get_declared_interfaces();

foreach($interfaces as $interface) {
    
}

$type = is_subclass_of('SeekableIterator','Iterator');
echo gettype($type);
echo $type;


echo get_parent_class('SeekableIterator');



$classes = get_declared_classes();
$data = array();
foreach($classes as $class) {
    $data[$class] = class_implements($class);
}

//echo '<pre>',print_r($data);
