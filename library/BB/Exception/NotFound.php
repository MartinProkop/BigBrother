<?php
class BB_Exception_NotFound extends Zend_Exception {
	public function __construct() {
		parent::__construct("Data not found", 404);
	}
}
