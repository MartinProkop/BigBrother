<?php
class BB_Controller_Abstract extends Zend_Controller_Action {
    /**
     * @var Application_Model_Row_User
     */
    private $_user;

    /**
     * @var BB_Controller_Response
     */
    protected $_responseObject;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
	// kontruktor predka
	parent::__construct($request, $response, $invokeArgs);

	// vytvoreni session a nacteni objektu uzivatele
	$session = new Zend_Session_Namespace("system");

	$paramSessid = $request->getParam("_sessid", false);
	$sessid = $paramSessid ? $paramSessid : $session->sessid;

	// nacteni session
	$tableSessions = new Application_Model_Sessions;
	$session = $tableSessions->find($sessid)->current();

	// vyhodnoceni nalezeni session
	if ($session) {
	    $this->_user = $session->findParentRow("Application_Model_Users", "user");
	} else {
	    $tableUsers = new Application_Model_Users;
	    $this->_user = $tableUsers->find(2)->current();
	}

	// kontrla opravneni uzivatele
	$tableAcl = new Application_Model_Permisions;

	// sestaveni dotazu
	$objType = $request->getModuleName();
	$controller = $request->getControllerName();

	$adapter = $tableAcl->getAdapter();

	$condition = array(
		$adapter->quoteInto("user_id = ?", $this->_user->id),
		$adapter->quoteInto("object_type like ?", $objType)
	);

	// vyhodnoceni, jestli se jedna o funkci, metodu nebo dotaz
	switch ($request->getUserParam("type")) {
	    case "fn":
		$condition[] = $adapter->quoteInto("function_name like ?", $controller);
		break;

	    default:
	    // jedna se o metodu
		$condition[] = $adapter->quoteInto("method_name like ?", $controller);
	}

	// nacteni opravneni
	$acl = $tableAcl->fetchRow($condition);

	if (!$acl && !$this->_user->is_admin) {
	    // uzovatel neni k akci opravnen
	    throw new BB_Exception_Forbidden;
	}

	// zamknuti uzivatele
	$this->_user->setReadOnly(true);

	// vytvoreni odpovedi
	$this->_responseObject = new BB_Controller_Response;
	$this->view->responseObject = $this->_responseObject;

	if (!Zend_Registry::isRegistered("responseObject"))
	    Zend_Registry::set("responseObject", $this->_responseObject);
    }

    /**
     * premosteni init funkce
     */
    public final function init() {

    }

    /**
     * vrace objekt s daty odpovedi
     * @return BB_Controller_Response
     */
    public function getResponseObject() {
	return $this->_responseObject;
    }

    /**
     * vraci referenci na radek uzivatele
     *
     * @return Appliction_Model_Row_User
     */
    public function getUser() {
	return $this->_user;
    }

    public function postDispatch() {
	$this->getResponse()->setHttpResponseCode($this->_responseObject->statusCode);
    }
}
