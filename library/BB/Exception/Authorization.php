<?php
class BB_Exception_Authorization extends Zend_Exception {
	public function __construct() {
		parent::__construct("Authorization needed", 401);
	}
}
