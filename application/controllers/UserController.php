<?php
class UserController extends BB_Controller {
	const VALIDTIME = "01:00:00";
	
	const MIN_PASSWORD_LENGTH = 5;
	
	public function init() {
		$request = $this->getRequest();
		$actionName = $request->getActionName();
		
		// vyhodnoceni, jslti se ma preskocit autorizace a autentizace
		if (substr($actionName, 0, 6) == "signin") {
			$this->skipAuthentization();
			$this->skipAuthorization();
			
			return;
		}
		
		if ((substr($actionName, 0, 7) == "signout") || substr($actionName, 0, 3) == "get" || substr($actionName, 0, 3) == "put") {
			$this->skipAuthorization();
		}
	}
	
	public function deleteAction() {
		/**
		 * @var Application_Model_Row_SystemUser
		 */
		$user = $this->_loadUserFromParam();
		
		if (!$user) throw new BB_Exception_NotFound;
		
		// pokud je uzivatel admin, je zakazano ho smazat
		if ($user->admin) throw new BB_Exception_Forbidden;
		
		//smazani dat
		$user->delete();
	}
	
	public function getAction() {
		$user = $this->_loadUserFromParam();
		
		// nacteni dat ACL
		$acl = $user->findDependentRowset("Application_Model_SystemUsersAcl", "user");
		
		$this->view->user = $user;
		$this->view->acl = $acl;
	}
	
	public function listAction() {
		// nacteni seznamu uzivatelu
		$tableUsers = new Application_Model_SystemUsers;
		
		$users = $tableUsers->fetchAll();
		$this->view->users = $users;
	}
	
	public function postAction() {
		// kontrola dat
		$data = array_merge(array("name" => "", "password" => ""), $this->getRequest()->getParam("user", array()));
		
		// validace dat
		$notEmpty = new Zend_Validate_NotEmpty;
		$alnum = new Zend_Validate_Alnum(false);
		
		if (!($notEmpty->isValid($data["name"]) && $alnum->isValid($data["name"]) && $notEmpty->isValid($data["password"]))) throw new BB_Exception_ValidateError;
		
		// kontrola jeslti uzivatel existuje
		$tableUsers = new Application_Model_SystemUsers;
		
		if ($tableUsers->fetchRow($tableUsers->getAdapter()->quoteInto("`name` like ?", $data["name"]))) throw new BB_Exception_Conflict;
		
		// vytvoreni noveho uzivatele
		/**
		 * @var Application_Model_Row_SystemUser
		 */
		$user = $tableUsers->createRow();
		$user->name = $data["name"];
		$user->setPassword($data["password"]);
		
		$user->save();
		
		$this->view->user = $user;
	}
	
	public function putAction() {
		/**
		 * @var Application_Model_Row_SystemUser
		 */
		$user = $this->_loadUserFromParam();
		
		if (!$user) throw new BB_Exception_NotFound();
		
		// kontorla opravneni menit uzivatele
		if ($user->id != $this->_user->id && !$this->_user->admin) throw new BB_Exception_Forbidden;
		
		// pokud je odeslano heslo, zmeni se heslo
		if (isset($data["password"])) $user->setPassword($data["password"]);
		
		// kontrola pozadavku na nastaveni prepinace admin
		if (isset($data["admin"]) && $this->getUser()->admin) {
			$user->admin = (bool) $data["admin"];
		} elseif (isset($data["admin"])) {
			throw new BB_Exception_Forbidden;
		}
		
		// pokud jsou odeslany informace ACL a zaroven je uzivatel admin, zmeni se ACL
		if (($acl = $this->getRequest()->getParam("acl", false)) && $this->getUser()->admin) {
			$tableAcl = new Application_Model_SystemUsersAcl;
			
			// odmazani starych dat
			$tableAcl->delete("UserId = " . $user->id);
			
			// zapsani novych dat
			if (!is_array($acl)) $acl = (array) $acl;
			
			/**
			 * @var Zend_Db_Adapter_Abstract
			 */
			$adapter = $tableAcl->getAdapter();
			
			// pole uchovavajici zpracovane pozadavky, aby se predeslo duplikacim
			$createdAcl = array();
			
			foreach ($acl as $item) {
				if (isset($item["objectType"], $item["method"])) {
					$objectType = strtolower($item["objectType"]);
					$method = strtolower($item["method"]);
					
					$hash = md5($objectType."#".$method);
					
					if (!in_array($hash, $createdAcl)) {
						$aclData = array(
							"UserId" => $user->id,
							"objectType" => $adapter->quote($item["objectType"]),
							"method" => $adapter->quote($item["method"])
						);
						
						$tableAcl->insert($aclData);
						
						$createdAcl[] = $hash;
					}
				}
			} 
			
		} elseif ($acl) {
			throw new BB_Exception_Forbidden;
		}
		
		$user->save();
	}
	
	public function signinAction() {
		// vyhodnoceni odeslanych dat
		$data = $this->getRequest()->getParam("user", array());
		
		$data = array_merge(array("name" => "", "password" => ""), $data);
		
		// nalezeni uzivatele
		$tableUsers = new Application_Model_SystemUsers();
		$adapter = $tableUsers->getAdapter();
		
		/**
		 * @var Application_Model_Row_SystemUser
		 */
		$user = $tableUsers->fetchRow($adapter->quoteInto("`name` like ?", $data["name"]));
		
		if (!$user) throw new BB_Exception_NotFound();
		
		if (!$user->isPasswordSame($data["password"])) throw new BB_Exception_NotFound;
		
		// zapsani nove session
		$tableSessions = new Application_Model_SystemUsersSessions;
		
		/**
		 * @var Application_Model_SystemUserSession
		 */
		$session = $tableSessions->createRow();
		$session->UserId = $user->id;
		$session->validTo = new Zend_Db_Expr("ADDTIME(NOW(), '" . self::VALIDTIME . "')");
		$session->params = Zend_Json::encode($_SERVER);
		
		$session->save();
		
		$this->getRequest()->setParam("_sessId", $session->sessId);
		setcookie("_sessId", $session->sessId, 0, "/");
		
		// zapsani vysledku do vystupu
		$this->view->user = $user;
	}
	
	public function signoutAction() {
		// nacteni session a jeji smazani z databaze
		$tableSession = new Application_Model_SystemUsersSessions;
		$session = $tableSession->find($this->getRequest()->getParam("_sessId"))->current();
		
		$session->delete();
		
		//anulace cookie
		setcookie("_sessId", null, 1, "/");
		
		//prenastaveni session v requestu
		$this->getRequest()->setParam("_sessId", false);
	}
	
	/**
	 * nacte uzivatele pomoci ID predane parametrem od klienta
	 * 
	 * @return Application_Model_Row_SystemUser
	 * @throw BB_Exception_NotFound
	 */
	protected function _loadUserFromParam() {
		$data = array_merge(array("id" => 0), $this->getRequest()->getParam("user", array()));
		
		// nalezeni uzivatele
		$tableUsers = new Application_Model_SystemUsers;
		
		/**
		 * @var Application_Model_Row_SystemUser
		 */
		$user = $tableUsers->find($data["id"])->current();
		
		if (!$user) throw new BB_Exception_NotFound();
		
		return $user;
	}
}
