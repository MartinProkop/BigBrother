<?php
class AclControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	protected $_tableUsers;
	
	protected $_tableSessions;
	
	protected $_tablePermisions;
	
	public function setUp() {
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		
		$this->_tableSessions = new Application_Model_Sessions;
		$this->_tableUsers = new Application_Model_Users;
		$this->_tablePermisions = new Application_Model_Permisions;
	}
	
	public function testDeleteCorrect() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(1);
		
		// vytvoreni dvou opravneni
		$acl = $this->_addPermision(2, "TEST", null, "A");
		
		// zapsani parametru
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"id" => $acl->id
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/delete");
		
		$this->assertAction("delete");
		
		// kontrola smazani
		$this->assertEquals(0, $this->_tablePermisions->fetchAll()->count());
	}
	
	public function testDeleteNotExists() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(1);
		
		// zapsani parametru
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"id" => 5
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/delete");
		
		$this->assertAction("error");
	}
	
	public function testDeleteUnAuth() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(2);
		
		// vytvoreni dvou opravneni
		$acl = $this->_addPermision(2, "TEST", null, "A");
		
		// zapsani parametru
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"id" => $acl->id
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/delete");
		
		$this->assertAction("error");
		
		// kontrola smazani
		$this->assertEquals(1, $this->_tablePermisions->fetchAll()->count());
	}
	
	public function testGetOwn() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(2);
		
		// vytvoreni dvou opravneni
		$this->_addPermision(2, "TEST", null, "A");
		$this->_addPermision(2, "TEST", "B", null);
		
		// vygenerovani parametru
		$params = array("_sessid" => $session->hash_id);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/get");
		
		// kontorla odpovedi
		$this->assertAction("get");
		$this->assertRegExp("/\"data\":\[(\{.*\},?){2}\]/", $this->getResponse()->getBody());
	}
	
	public function testGetOtherUnauth() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(2);
		
		// vytvoreni dvou opravneni
		$this->_addPermision(1, "TEST", null, "A");
		$this->_addPermision(1, "TEST", "B", null);
		
		// vygenerovani parametru
		$params = array("_sessid" => $session->hash_id, "user" => array("id" => 1));
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/get");
		
		// kontorla odpovedi
		$this->assertAction("error");
	}
	
	public function testGetOtherCorrect() {
		// prihlaseni se jako root
		$session = $this->_simulateSignIn(1);
		
		// vytvoreni dvou opravneni
		$this->_addPermision(2, "TEST", null, "A");
		$this->_addPermision(2, "TEST", "B", null);
		
		// vygenerovani parametru
		$params = array("_sessid" => $session->hash_id, "user" => array("id" => 2));
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/get");
		
		// kontorla odpovedi
		$this->assertAction("get");
		$this->assertRegExp("/\"data\":\[(\{.*\},?){2}\]/", $this->getResponse()->getBody());
	}
	
	public function testPostCorrectFunc() {
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"function_name" => "FUNC"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertAction("post");
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(1, $rowset->count());
		
		$row = $rowset->current();
		
		$this->assertTrue($row->user_id == 2);
		$this->assertTrue(!strcmp($row->object_type, "TEST"));
		$this->assertTrue(!strcmp($row->function_name, "FUNC"));
		$this->assertNull($row->method_name);
	}
	
	public function testPostCorrectMethod() {
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => "METHOD"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertAction("post");
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(1, $rowset->count());
		
		$row = $rowset->current();
		
		$this->assertTrue($row->user_id == 2);
		$this->assertTrue(!strcmp($row->object_type, "TEST"));
		$this->assertTrue(!strcmp($row->method_name, "METHOD"));
		$this->assertNull($row->function_name);
	}
	
	public function testPostUnauth() {
		$session = $this->_simulateSignIn(2);
		
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => "METHOD"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertAction("error");
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(0, $rowset->count());
	}
	
	public function testPostConflict1() {
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => null,
				"function_name" => null
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertResponseCode(400);
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(0, $rowset->count());
	}
	
	public function testPostConflict2() {
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"_sessid" => $session->hash_id,
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => "fnc1",
				"function_name" => "mtd1"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertResponseCode(409);
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(0, $rowset->count());
	}
	
	public function testPostGuestNoValidSessid() {
		$params = array(
			"_sessid" => "abc",
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => "fnc1",
				"function_name" => "mtd1"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertAction("error");
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(0, $rowset->count());
	}
	
	public function testPostGuestNoSessid() {
		$params = array(
			"permision" => array(
				"user_id" => 2,
				"object_type" => "TEST",
				"method_name" => "fnc1",
				"function_name" => "mtd1"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/acl/post");
		
		$this->assertAction("error");
		
		$rowset = $this->_tablePermisions->fetchAll();
		$this->assertEquals(0, $rowset->count());
	}
	
	public function tearDown() {
		$this->_tablePermisions->delete("1");
		$this->_tableSessions->delete("1");
		$this->_tableUsers->delete("id > 2");
	}
	
	protected function _addPermision($userId, $objType, $methodName = null, $funcName = null) {
		$permision = $this->_tablePermisions->createRow();
		
		$permision->user_id = $userId;
		$permision->object_type = $objType;
		$permision->method_name = $methodName;
		$permision->function_name = $funcName;
		
		$permision->save();
		return $permision;
	}
	
	protected function _createUser($login, $psw) {
		$user = $this->_tableUsers->createRow();
		
		$user->login = $login;
		$user->setPassword($psw);
		$user->save();
		
		return $user;
	}
	
	protected function _simulateSignIn($id) {
		$session = $this->_tableSessions->createRow();
		$session->user_id = $id;
		$session->save();
		
		return $session;
	}
}
