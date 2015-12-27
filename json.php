<?php

class user implements JsonSerializable{
    private $name ="scott";
    private $privatedata = array('id'=>10, 'name'=>'scott','addr'=>'bokaro');

    public function test(){
        return array("one","two");    
    }
    public function jsonSerialize() {
            return $this->privatedata;
   }    
}

$obj = new user();

$serial = json_encode($obj);

echo $serial;
