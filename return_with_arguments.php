<?php

/**
    return type with no argument
*/

function test(){
    return 10;    
}

$var = test();

echo gettype($var);

echo $var;
