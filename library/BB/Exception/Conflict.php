<?php
class BB_Exception_Conflict extends Zend_Exception {
	public function __construct() {
		parent::__construct("Data conflict error", 409);
	}
}
