<?php

$array1 = array("a" => "green", "b" => "brown", "c" => "blue","test", "red","next");
$array2 = array("a" => "green", "yellow","red","text");
$result = array_diff_assoc($array1, $array2);
print_r($result);

