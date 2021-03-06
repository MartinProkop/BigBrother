<?php
class Application_Model_Row_User extends Zend_Db_Table_Row_Abstract {
	public function isPasswordSame($psw) {
		return strcmp($this->password, $this->hashPassword($psw)) ? false : true;
	}
	
	public function hashPassword($psw) {
		return sha1($psw . $this->salt);
	}
	
	public function setPassword($psw) {
		$this->password = $this->hashPassword($psw);
		
		return $this;
	}
}
