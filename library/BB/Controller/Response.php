<?php
class BB_Controller_Response extends stdClass {
	public $status = "UNKNOWN";
	
	public $statusCode = 200;
	
	public $message = "";
	
	public $data = array();
	
	public $objectType = "UNKNOWN";
	
	public function __construct($objectType = "UNKNOWN") {
		$this->objectType = $objectType;
	}
	
	public function setStatus($status, $statusCode) {
		$this->status = $status;
		$this->statusCode = $statusCode;
		
		return $this;
	}
	
	public function setOk($status = "OK", $statusCode = 200) {
		$this->setStatus($status, $statusCode);
	}
}
