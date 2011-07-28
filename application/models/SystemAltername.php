<?php
class Application_Model_SystemAltername extends Zend_Db_Table_Abstract {
	protected $_id;
	
	protected $_PrivateName;
	
	protected $_PublicName;
	
	protected $_name = "system_altername";
	
	protected $_primary = "id";
	
	protected $_sequence = true;
}