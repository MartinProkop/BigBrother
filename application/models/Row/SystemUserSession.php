<?php
class Application_Model_Row_SystemUserSession extends Zend_Db_Table_Row_Abstract {
	public function save() {
		if (!$this->sessId) {
			$this->sessId = $this->_generateSessId();
		}
		
		return parent::save();
	}
	
	protected function _generateSessId() {
		$retVal = sha1(time() . serialize($_SERVER));
		
		$retVal = sha1($retVal);
		$tableSessions = $this->getTable();
		
		while ($tableSessions->find($retVal)->current()) {
			$retVal = sha1($retVal . microtime());
		}
		
		return $retVal;
	}
}