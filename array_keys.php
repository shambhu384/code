<?php

/**
* indexed array
*/
//$array = ['first'=>1,'third'=>3,'mynumber'=>4,5,7];

//echo "\t".gettype($array)."\n";

//print_r(array_keys($array));


/**
 Escape sequence character are not working in '' single qoute
 ex \s 

we can use escape charater in "" qoutes
*/

/**
 diff II) within double qoutes a variable give value of the variable
*/

$name  ='Scott';

echo '$name';
// faster
echo 'this is '.$name.' not my name';
//slower
echo "\n \t this is $name not my name";

//echo 'this is {$name} not my name';
$test = 'test';
echo "this is not {$test()} my name";

function test(){
    return 'Hello';    
}


