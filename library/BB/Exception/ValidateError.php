<?php
class BB_Exception_ValidateError extends Zend_Exception {
	public function __construct() {
		parent::__construct("Data is not valid", 400);	
	}
}
