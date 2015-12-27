<?php
$socket = stream_socket_client('tcp://127.0.0.1:1200');

if(!$socket){
    echo $errstr;
} else {
    
    while(!feof($socket)) {
        echo fread($socket,100);
    }

    fclose($socket);
}

