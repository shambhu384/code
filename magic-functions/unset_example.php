<?php
  class WebDeveloper {
    public $info = array();
   
    public function __isset($item) {
      return isset($this->info[$item]);
    }
    public function __unset($item) {
      unset($this->info[$item]);
    }
  }
  /* create object */
  $obj = new WebDeveloper();
  /* before set value */
  echo '<pre>'.print_r($obj);
  /* set value */
  $obj->info=array('name'=>'scott');
  /* after set value */
  echo '<pre>'.print_r($obj);
  if(isset($obj->name)){ 
    echo 'value has set<br/>';
  }
  /* after unset value */
  unset($obj->name);

  echo '<pre>'.print_r($obj);
?>
