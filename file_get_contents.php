<?php
/**
PHP provide us two function to read file and write file

file_get_contents('filename');

file_put_contents('filename', fileresourse);

*/
$file = file_get_contents('img.png');

file_put_contents('img2.png',$file);

