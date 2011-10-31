<?php
class AclController extends Zend_Controller_Action {
	protected $_tableAcl;
	
	protected $_user;
	
	const ROOT_ID = 1;
	
	const GUEST_ID = 2;
	
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
		if ((substr($actionName, 0, 6) != "get") && !$this->_user->is_admin) {
			// uzivatel neni opravnen k pseudoobjektu pristupovat
			throw new Zend_Acl_Exception("You are not permited to requested action", 403);
		}
		
		$this->_tableAcl = new Application_Model_Permisions;
	}

	public function deleteAction() {
		// nacteni dat pozadavku
		$data = $this->getRequest()->getParam("permision", array());
		$data = array_merge(array("id" => 0), $data);
		
		// nacteni dat z databaze
		$acl = $this->_tableAcl->find($data["id"])->current();
		
		// kontrola nacteni
		if (!$acl) {
			throw new BB_Exception_NotFound;
		}
		
		// smazani opravneni
		$acl->delete();
		$this->view->response->setOk();
	}
	
	public function getAction() {
		// nacteni dat pozdavaku
		$data = $this->getRequest()->getParam("user", array());
		$data = array_merge(array("id" => 0), $data);
		
		if ($data["id"] && $this->_user->is_admin) {
			// kontrola, jestli uzivatel existuje
			$tableUsers = new Application_Model_Users;
			$user = $tableUsers->find($data["id"])->current();
			
			if (!$user)
				throw new BB_Exception_NotFound();
		} elseif ($data["id"]) {
			throw new BB_Exception_Forbidden;
		} else {
			$user = $this->_user;
		}
		
		// nacteni dat
		$acls = $user->findDependentRowset($this->_tableAcl, "user");
		
		// zapsani odpovedi
		$this->view->response->data = $acls->toArray();
		$this->view->response->setOk();
	}
	
	public function postAction() {
		// nacteni dat pozadavku
		$data = $this->getRequest()->getParam("permision", array());
		$data = array_merge(array("user_id" => 0, "object_type" => "", "function_name" => null, "method_name" => null), $data);
		
		// kontrola dat
		try {
			// kontrola validity jmena funkce nebo metody
			if (is_null($data["method_name"]) && is_null($data["function_name"]))
				throw new BB_Exception_NoData;
			
			if (!empty($data["method_name"]) && !empty($data["function_name"]))
				throw new BB_Exception_Conflict;
			
			// kontrola existence uzivatele
			$tableUsers = new Application_Model_Users;
			$user = $tableUsers->find($data["user_id"])->current();
			
			if (!$user)
				throw new BB_Exception_NotFound;
		} catch (Zend_Exception $e) {
			// nastaveni odpovedi a ukonceni behu
			$this->view->response->setStatus($e->getMessage(), $e->getCode());
			return;
		}
		
		// vytvoreni a ulozeni radku
		$acl = $this->_tableAcl->createRow();
		$acl->user_id = $user->id;
		$acl->object_type = $data["object_type"];
		$acl->method_name = $data["method_name"];
		$acl->function_name = $data["function_name"];
		
		$acl->save();
		
		// nastavnei dat
		$this->view->response->data = $acl->toArray();
		$this->view->response->setOk();
	}

	public function postDispatch() {
		$this->getResponse()->setHttpResponseCode($this->view->response->statusCode);
	}
}
