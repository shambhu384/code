<?php

/**
* No return with argument
*/


function add($a,$b){
    echo $a+$b;
}

$a = add(5,8);
echo gettype($a);
echo $a;
