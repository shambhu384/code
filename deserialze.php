<?php

$str = 'a:3:{s:8:"repeatid";i:0;s:9:"timestart";i:1448466000;s:4:"name";s:8:"New quiz";}';

$obj = unserialize($str);
var_dump($obj);

echo serialize($obj);
