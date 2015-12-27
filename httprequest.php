<?php
header('Content-Type:application/json');
echo json_encode(array("name"=>'scott'),JSON_PRETTY_PRINT);
