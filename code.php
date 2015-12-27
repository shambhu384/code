<?php

$array = unserialize('a:5:{s:2:"id";s:1:"1";s:8:"courseid";s:1:"2";s:8:"quizname";s:8:"New quiz";s:6:"course";s:13:"features demo";s:8:"question";a:1:{i:1;a:2:{s:12:"questiontext";s:26:"Question text goes here...";s:7:"answers";a:5:{i:0;a:2:{s:6:"option";s:14:"option 1&nbsp;";s:8:"fraction";i:0;}i:1;a:2:{s:6:"option";s:13:"option 2 true";s:8:"fraction";i:1;}i:2;a:2:{s:6:"option";s:8:"option 3";s:8:"fraction";i:0;}i:3;a:2:{s:6:"option";s:8:"option 4";s:8:"fraction";i:0;}i:4;a:2:{s:6:"option";s:8:"option 5";s:8:"fraction";i:0;}}}}}');

echo '<pre>',print_r($array);
