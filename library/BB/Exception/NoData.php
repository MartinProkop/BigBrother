<?php
class BB_Exception_NoData extends Zend_Exception {
	public function __construct() {
		parent::__construct("Data send to server is not complete", 400);
	}
}
