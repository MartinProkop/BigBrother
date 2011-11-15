<?php
class UserController extends Zend_Controller_Action {
	const VALIDTIME = "01:00:00";
	
	const MIN_PASSWORD_LENGTH = 5;
	
	const ROOT_ID = 1;
	
	const GUEST_ID = 2;
	
	/**
	 * @var Application_Model_Row_User
	 */
	private $_user;
	
	public function init() {
		$this->view->response = new BB_Controller_Response("SYSTEM");
		
		$request = $this->getRequest();
		$actionName = $request->getActionName();
		
		// nacteni uzivatele
		$session = new Zend_Session_Namespace("system");
		$sessid = $this->getRequest()->getParam("_sessid", false);
		
		$sessid = $sessid ? $sessid : $session->sessid;
		
		// pokud id session je definovano, nacte se uzivatel pres toto id
		if ($sessid) {
			// nacteni session
			$tableSessions = new Application_Model_Sessions;
			$userSession = $tableSessions->find($sessid)->current();
			
			// kontrola jestli je session platne
			if ($userSession) {
				// update session a ulozeni
				$userSession->used_at = new Zend_Db_Expr("NOW()");
				$userSession->save();
				
				// nacteni uzivatele
				$this->_user = $userSession->findParentRow("Application_Model_Users", "user");
			} else {
				// session neni platne, nacte se guest
				$tableUsers = new Application_Model_Users;
				$this->_user = $tableUsers->find(self::GUEST_ID)->current();
			}
		} else {
			// id neni definovano, nacte se host
			$tableUsers = new Application_Model_Users;
			$this->_user = $tableUsers->find(self::GUEST_ID)->current();
		}
		
		// kontrola opravneni
		if ((substr($actionName, 0, 6) != "signin") && (substr($actionName, 0, 7) != "signout") && (substr($actionName, 0, 3) != "get") && (substr($actionName, 0, 3) != "put") && !$this->_user->is_admin) {
			// uzivatel neni opravnen k pseudoobjektu pristupovat
			throw new Zend_Acl_Exception("You are not permited to requested action", 403);
		}
	}
	
	public function deleteAction() {
		// ziskani dat a nacteni uzivatele
		$data = $this->getRequest()->getParam("user", array());
		$data = array_merge(array("id" => 0), $data);
		
		$user = $this->_loadUser($data);
		
		// pokud se uzivatel snazi smazat sam sebe, zabrani se mu v tom
		if ($user->id == $this->_user->id)
			throw new BB_Exception_Conflict;
		
		// pokud se uzivatel snazi smazat ucet root nebo guest, zabrani se mu v tom
		if ($user->id == self::ROOT_ID || $user->id == self::GUEST_ID)
			throw new BB_Exception_Conflict;
		
		// pokud je uzivatel admin, je zakazano ho smazat
		if ($user->is_admin) throw new BB_Exception_Forbidden;
		
		//smazani dat
		$user->delete();
		
		$this->view->response->setOk();
	}
	
	public function getAction() {
		// ziskani dat a nacteni uzivatele
		$data = $this->getRequest()->getParam("user", array());
		$data = array_merge(array("id" => 0), $data);
		
		$user = $this->_loadUser($data);
		
		// kontrola opravneni
		if ($user->id != $this->_user->id && !$this->_user->is_admin)
			throw new BB_Exception_Forbidden;
		
		// nacteni dat ACL
		$acl = $user->findDependentRowset("Application_Model_Permisions", "user");
		$arrUser = $user->toArray();
		
		unset($arrUser["salt"], $arrUser["password"]);
		
		$this->view->response->data["user"] = $arrUser;
		$this->view->response->data["acl"] = $acl->toArray();
	}
	
	public function listAction() {
		// nacteni seznamu uzivatelu
		$tableUsers = new Application_Model_Users;
		
		$users = $tableUsers->fetchAll();
		$this->view->response->setOk();
		$this->view->response->data = $users->toArray();
	}
	
	public function postAction() {
		// kontrola dat
		$data = array_merge(array("login" => "", "password" => ""), $this->getRequest()->getParam("user", array()));
		
		// validace dat
		$notEmpty = new Zend_Validate_NotEmpty;
		$alnum = new Zend_Validate_Alnum(false);
		
		if (!($notEmpty->isValid($data["login"]) && $alnum->isValid($data["login"]) && $notEmpty->isValid($data["password"]))) throw new BB_Exception_ValidateError;
		
		// kontrola jeslti uzivatel existuje
		$tableUsers = new Application_Model_Users;
		
		if ($tableUsers->fetchRow($tableUsers->getAdapter()->quoteInto("`login` like ?", $data["login"]))) throw new BB_Exception_Conflict;
		
		// vytvoreni noveho uzivatele
		/**
		 * @var Application_Model_Row_User
		 */
		$user = $tableUsers->createRow();
		$user->login = $data["login"];
		$user->setPassword($data["password"]);
		
		$user->save();
		
		$this->view->response->data = $user->toArray();
		$this->view->response->setOk();
	}
	
	public function putAction() {
		// nacteni uzivatele
		$data = $this->getRequest()->getParam("user", array());
		$data = array_merge(array("id" => 0), $data);
		
		$user = $this->_loadUser($data);
		
		if (!$user) throw new BB_Exception_NotFound();
		
		// kontrola, jeslti uzivatel upravuje jineho uzivatele a v tom pripade, jeslti je admin
		if ($user->id != $this->_user->id && !$this->_user->is_admin)
			throw new BB_Exception_Forbidden;
		
		// pokud je odeslano heslo, zmeni se heslo
		if (isset($data["password"])) $user->setPassword($data["password"]);
		
		// kontrola pozadavku na nastaveni prepinace is_admin
		if (isset($data["is_admin"]) && $this->_user->is_admin) {
			$user->is_admin = (bool) $data["is_admin"];
		} elseif (isset($data["is_admin"])) {
			throw new BB_Exception_Forbidden;
		}
		
		 // kontrola pozasavku na nastaveni prepinace is_blocked
		 if (isset($data["is_blocked"]) && $this->_user->is_admin) {
		 	$user->is_blocked = (bool) $data["is_blocked"];
		 } elseif (isset($data["is_blocked"])) {
		 	throw new BB_Exception_Forbidden;
		 }
		
		$user->save();
	}
	
	public function signinAction() {
		// vyhodnoceni odeslanych dat
		$data = $this->getRequest()->getParam("user", array());
		
		$data = array_merge(array("name" => "", "password" => ""), $data);
		
		// nalezeni uzivatele
		$tableUsers = new Application_Model_Users();
		$adapter = $tableUsers->getAdapter();
		
		/**
		 * @var Application_Model_Row_User
		 */
		$user = $tableUsers->fetchRow($adapter->quoteInto("`login` like ?", $data["login"]));
		
		if (!$user) throw new BB_Exception_NotFound();
		
		if (!$user->isPasswordSame($data["password"])) {
			throw new BB_Exception_NotFound;
		}
		
		// zapsani nove session
		$tableSessions = new Application_Model_Sessions;
		
		/**
		 * @var Application_Model_Session
		 */
		$session = $tableSessions->createRow();
		$session->user_id = $user->id;
		$session->used_at = new Zend_Db_Expr("NOW()");
		
		$session->save();
		
		$this->getRequest()->setParam("_sessid", $session->hash_id);
		@setcookie("sessid", $session->hash_id, 0, "/");
		
		// zapsani vysledku do vystupu
		$this->view->response->data = $user->toArray();
		$this->view->response->setOk();
	}
	
	public function signoutAction() {
		//prenastaveni session v requestu
		$sessid = $this->getRequest()->getParam("_sessid", "");
		
		$this->getRequest()->setParam("_sessid", false);
		$this->view->response->setOk();
		
		// nacteni session a jeji smazani z databaze
		$tableSession = new Application_Model_Sessions;
		$session = $tableSession->find($sessid)->current();
		
		if (!$session)
			return;
		
		$session->delete();
		
		//anulace cookie
		@setcookie("_sessId", null, 1, "/");
	}
	
	/**
	 * nacte uzivatele pomoci ID predane parametrem od klienta
	 * 
	 * @return Application_Model_Row_SystemUser
	 * @throw BB_Exception_NotFound
	 */
	protected function _loadUser($data) {
		if ($data["id"] && $this->_user->is_admin) {
			$tableUsers = new Application_Model_Users;
			$user = $tableUsers->find($data["id"])->current();
		} elseif ($data["id"]) {
			throw new BB_Exception_Forbidden;
		} else {
			$user = $this->_user;
		}
		
		return $user;
	}
	
	public function postDispatch() {
		$this->getResponse()->setHttpResponseCode($this->view->response->statusCode);
	}
}
