<?php
class Application_Model_SystemUsersSessions extends Zend_Db_Table_Abstract {
	protected $_name = "system_users_sessions";
	
	protected $_primary = "sessId";
	
	protected $_sequence = false;
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "UserId",
			"refTableClass" => "Application_Model_SystemUsers",
			"refColumns" => "id"
		)
	);
	
	protected $_rowClass = "Application_Model_Row_SystemUserSession";
}
