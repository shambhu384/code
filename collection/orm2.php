<?php
class User { }

$rc = new ReflectionClass('User');
var_dump($rc->getDocComment())
?>
