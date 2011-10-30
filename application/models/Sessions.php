<?php
class Application_Model_Sessions extends Zend_Db_Table_Abstract {
	protected $_name = "sessions";
	
	protected $_primary = "hash_id";
	
	protected $_sequence = false;
	
	protected $_rowClass = "Application_Model_Row_Session";
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "user_id",
			"refTableClass" => "Application_Model_Users",
			"refColumns" => "id"
		)
	);
}
