<?php
require_once('lib/entry.php');
class template extends entry{
	public function __construct(){
		parent::__construct();
	}

	public function run(){
		$this->param('method');
		if(in_array($this->method,[
			//available method
			'egFunc'
		])){
			$this->{$this->method}();
		}else{
			error('error method：'.$this->method);
		}
	}

	private function egFunc(){
		//do something
	}
}

runApp();