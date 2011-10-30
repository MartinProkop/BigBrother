<?php
class Application_Model_Data extends BB_Db_Table_Data_Abstract {
	protected $_name = "data";
	
	protected $_primary = "uuid";
	
	protected $_sequence = false;
	
	protected $_referenceMap = array(
		"user" => array(
			"columns" => "user_id",
			"refTableClass" => "Application_Model_Users",
			"refColumns" => "id"
		)
	);
}
