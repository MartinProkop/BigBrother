<?php
class Application_Model_Permisions extends Zend_Db_Table_Abstract {
	protected $_name = "permisions";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "user_id",
			"refTableClass" => "Application_Model_Users",
			"refColumns" => "id"
		)
	);
}
