<?php
class Application_Model_SystemUsersAcl extends Zend_Db_Table_Abstract {
	protected $_id;
	
	protected $_UserId;
	
	protected $_ObjectType;
	
	protected $_method;
	
	protected $_name = "system_users_acl";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "UserId",
			"refTableClass" => "Application_Model_SystemUsers",
			"refColumns" => "id"
		)
	);
}
