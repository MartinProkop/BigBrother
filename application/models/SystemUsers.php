<?php
class Application_Model_SystemUsers extends Zend_Db_Table_Abstract {
	protected $_name = "system_users";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
	
	protected $_dependentTables = array(
		"Application_Model_SystemInterLog",
		"Application_Model_SystemExternLog",
		"Application_Model_SystemUsersAcl",
		"Application_Model_SystemUsersSessions"
	);
	
	protected $_rowClass = "Application_Model_Row_SystemUser";
	
	public function createRow(array $data = array(), $defaultSource = null) {
		$row = parent::createRow($data, $defaultSource);
		$row->salt = sha1(time() , microtime());
	}
}
