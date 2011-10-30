<?php
class Application_Model_Logs extends Zend_Db_Table_Abstract {
	protected $_name = "logs";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "user_id",
			"refTableClass" => "Application_Model_Users",
			"columns" => "id"
		)
	);
}
