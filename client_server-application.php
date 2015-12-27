<?php

$server_socket = stream_socket_server("tcp://192.168.1.9:1200");

class Demo implements Serializable {

    private $data;

    public function __construct() {
        $this->data = "My private data";
    }

    public function serialize() {
        return serialize($this->data);
    }

    public function unserialize($data) {
        $this->data = unserialize($data);
    }

    public function getData() {
        return $this->data;
    }

}

$user = new Demo();

if ($server_socket) {
    echo 'Server started..';
}

while ($socket = stream_socket_accept($server_socket, 30000)) {

    fwrite($socket, serialize($user));
    fclose($socket);
}
fclose($server_socket);



