<?php
class Application_Model_SystemExternLog extends Zend_Db_Table_Abstract {
	protected $_id;
	
	protected $_UserId;
	
	protected $_date;
	
	protected $_objectType;
	
	protected $_objectId;
	
	protected $_method;
	
	protected $_getParam;
	
	protected $_postParam;
	
	protected $_action;
	
	protected $_name = "system_extern_log";
	
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
