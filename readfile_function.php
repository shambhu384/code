<?php
/**
PHP provides us a function to read any file
and response into the browser with passing content-type 
in header function.
*/
header('Content-Type:image/gif');
readfile('img.png');
