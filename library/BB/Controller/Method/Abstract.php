<?php
abstract class BB_Controller_Method_Abstract extends Zend_Controller_Action {
	private $_dataContainer;
	
	private $_queryObject;
	
	private $_user;
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		// vytvoreni session a nacteni objektu uzivatele
		$session = new Zend_Session_Namespace("system");
		
		$paramSessid = $this->getRequest()->getParam("_sessid", false);
		$sessid = $paramSessid ? $paramSessid : $session->sessid;
	} 
}
