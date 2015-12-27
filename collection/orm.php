<?php
function __autoload($class){
	require $class.'.php';
}
class Emp{
		private $id;
		private $name;
		private $address;

		public function getId(){
			return $this->id;
		}
		public function getName(){
			return $this->name;
		}
		public function getAddress(){
			return $this->address;
		}

		public function setValues($id,$name,$address){
			$this->id = $id;
			$this->name=$name;
			$this->address =$address;
		}
}

$emp = new Emp();
$emp->setValues(1,'scott','bokaro');
$orm = new PHPOrm();
$orm->save(new Emp);

?>
