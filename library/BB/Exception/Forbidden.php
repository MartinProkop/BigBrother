<?php
class BB_Exception_Forbidden extends Zend_Exception {
	public function __construct() {
		parent::__construct("No permision for this action", 403);
	}
}
