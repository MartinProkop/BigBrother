<?php
class Application_Model_SystemInterLog extends Zend_Db_Table_Abstract {
	protected $_id;
	
	protected $_UserId;
	
	protected $_date;
	
	protected $_TableName;
	
	protected $_ObjectId;
	
	protected $_actionType;
	
	protected $_action;
	
	protected $_name = "system_inter_log";
	
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
