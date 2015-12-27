<?php
/**
 Interface cann't be instantiate because interface is fully unimplemented(No Body) structure.
 variable declaration not allowed, Interface inherit

*/
interface user{
    const text = 1;
    public function get_name();
    public function get_userdata();
}

class facebook_user implements user{
    
    public function get_name(){
         return 'faceebook user';
    }

    public function get_userdata(){}
        
}

class twitter_user implements user{
    
    public function get_name(){ 
        return 'twitter user';    
    }

    public function get_userdata(){}
}

class linkdin_user{
  public function get_name(){
    return 'linkdin user';     
   
 }   
}


function load (user $user){
  echo $user->get_name();
}


$fb = new facebook_user();

$tw = new twitter_user();



load(new $argv[1]);


