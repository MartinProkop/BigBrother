<?php
class BB_Controller extends Zend_Controller_Action {
	protected $_skipAuthentization = false;
	
	protected $_skipAuthorization = false;
	
	protected $_user = null;
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		// zavolani kontruktoru predka
        parent::__construct($request, $response, $invokeArgs);
		
		$authorized = true;
		$authentized = true;
		
		if (!$this->_skipAuthentization) $authentized = $this->_authentize();
		if (!$this->_skipAuthorization) $authorized = $this->_authorize();
		
		if (!($authentized && $authorized)) throw new Zend_Exception("Not permited", 403);
    }
	
	/**
	 * vraci aktualniho uzivatele
	 * 
	 * @return Application_Model_Row_SystemUser
	 */
	public function getUser() {
		return $this->_user;
	}
	
	/**
	 * nastavi, zda ma byt preskcena autentizace
	 * 
	 * @param bool $skip pokud je TRUE, bude autentizace preskocena
	 * @return BB_Controller
	 */
	public function skipAuthentization($skip = true) {
		$this->_skipAuthentization = (bool) $skip;
		
		return $this;
	}
	
	/**
	 * nastavi, zda ma byt preskocena autorizace
	 * 
	 * @param bool $skip pokud je TRUE, bude autorizace preskocena
	 * @return BB_Controller
	 */
	public function skipAuthorization($skip = true) {
		$this->_skipAuthorization = (bool) $skip;
		
		return $this;
	}
	
	/**
	 * provede autentizaci uzivatele. 
	 * Pokud vraci TRUE, byl uzivatel nalezen
	 * 
	 * @return bool
	 */
	protected function _authentize() {
		// kontrola odeslanych informaci o autentizaci
		$sessId = $this->getRequest()->getParam("_sessId", false);
		
		if (!$sessId) return false;
		
		// nacteni a kontrola validity 
		$tableSessions = new Application_Model_SystemUsersSessions();
		$session = $tableSessions->fetchRow(array($tableSessions->getAdapter()->quoteInto("sessId like ?", $sessId), "validTo >= NOW()"));
		
		if ($session) {
			$this->_user = $session->findParentRow("Application_Model_SystemUsers");
			return true;
		}
		
		return false;
	}
	
	/**
	 * provede autorizaci uzivatele
	 * Vraci TRUE, pokud je uzivatel opravnen akci provest
	 * 
	 * @return bool
	 */
	protected function _authorize() {
		// pokud uzivatel neni prihlasen, automaticky se vraci FALSE
		if (!$this->_user) return false;
		
		// nactei parametru pozdavaku
		$request = $this->getRequest();
		
		$module = $request->getModuleName();
		$method = $request->getControllerName();
		
		// pokud je modul DEFAULT, musi byt uzivatel admin
		if ($module == "default" && !$this->_user->admin) return false;
		
		// kontrola v tabulce ACL
		$tableAcl = new Application_Model_SystemUsersAcl;
		$adapter = $tableAcl->getAdapter();
		
		$row = $tableAcl->fetchRow(array(
			$adapter->quoteInto("UserId = ?", $this->_user->id),
			$adapter->quoteInto("ObjectType like ?", $module),
			$adapter->quoteInto("method like ?", $method)));
		
		return $row ? true : false;
	}
}
