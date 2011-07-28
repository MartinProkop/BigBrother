<?php
class Application_Model_SystemUsers extends Zend_Db_Table_Abstract {
	protected $_id;
	
	protected $_username;
	
	protected $_password;
	
	protected $_salt;
	
	protected $_created;
	
	protected $_name = "system_users";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
	
	protected $_dependentTables = array(
		"Application_Model_SystemInterLog",
		"Application_Model_SystemExternLog"
	);
}
