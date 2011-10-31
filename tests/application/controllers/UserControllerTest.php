<?php
class UserControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	const ROOT_PSW = "heslo1";
	
	const GUEST_PSW = "1";
	
	protected $_tableUsers;
	
	protected $_tableSessions;
	
	public function setUp() {
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		
		$this->_tableSessions = new Application_Model_Sessions;
		$this->_tableUsers = new Application_Model_Users;
	}
	
	public function testInitGuest() {
		$this->_clearAll();
		
		$params = array("_sessid" => "a");
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/get");
		$this->assertAction("get");
		
		$this->assertRegExp("/guest/", $this->getResponse()->getBody());
	}
	
	public function testDelete() {
		$this->_clearAll();
		
		$user = $this->createUser("test", "test");
		
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"user"=> array("id" => $user->id),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/delete");
		
		$this->assertAction("delete");
		
		$this->assertNull($this->_tableUsers->find($user->id)->current());
	}
	
	public function testDeleteSelf() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"user"=> array("id" => 1),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/delete");
		
		$this->assertAction("error");
	}
	
	public function testDeleteGuest() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"user"=> array("id" => 2),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/delete");
		
		$this->assertAction("error");
	}
	
	public function testDeleteAdmin() {
		$this->_clearAll();
		
		$user = $this->createUser("test", "test");
		$user->is_admin = 1;
		$user->save();
		
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"user"=> array("id" => $user->id),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/delete");
		
		$this->assertAction("error");
	}
	
	public function testGetRoot() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		$params = array(
			"user" => array(
				"id" => 2
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/get");
		
		$this->assertAction("get");
	}
	
	public function testGetGuest() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(2);
		
		$params = array(
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/get");
		
		$this->assertAction("get");
	}
	
	public function testGetUnauth() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(2);
		
		$params = array(
			"user" => array(
				"id" => 1
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/get");
		
		$this->assertAction("error");
	}
	
	public function testListRegular() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// odeslani pozadavku
		$this->getRequest()->setParams(array("_sessid" => $session->hash_id));
		$this->dispatch("/system/user/list");
		
		$this->assertAction("list");
	}
	
	public function testListUnauth() {
		$this->_clearAll();
		
		$this->dispatch("/system/user/list");
		$this->assertAction("error");
	}
	
	public function testPostRegular() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// nastaveni parametru
		$params = array(
			"user" => array(
				"login" => "test",
				"password" => "ahoj"
			),
			"_sessid" => $session->hash_id
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		// kontrola dat
		$rows = $this->_tableUsers->fetchAll(null, "id desc");
		
		// obecna kontrola
		$this->assertAction("post");
		$this->assertEquals(3, $rows->count());
		
		// kontrola noveho radku
		$row = $rows->current();
		
		$this->assertEquals($params["user"]["login"], $row->login);
		$this->assertEquals(0, $row->is_admin);
		$this->assertEquals(0, $row->is_blocked);
		$this->assertEquals($row->hashPassword($params["user"]["password"]), $row->password);
	}
	
	public function testPostNotAuthorized() {
		$this->_clearAll();
		
		// nastaveni parametru - _sessid neni nastaven - ppouzije se guest
		$params = array(
			"user" => array(
				"login" => "test",
				"password" => "ahoj"
			)
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		$this->assertAction("error");
	}
	
	public function testPostLoginExists() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// nastaveni parametru
		$params = array(
			"user" => array(
				"login" => "root",
				"password" => "ahoj"
			),
			"_sessid" => $session->hash_id
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		$this->assertAction("error");
	}
	
	public function testPostLoginEmpty() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// nastaveni parametru
		$params = array(
			"user" => array(
				"login" => "",
				"password" => "ahoj"
			),
			"_sessid" => $session->hash_id
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		$this->assertAction("error");
	}
	
	public function testPostPasswordEmpty() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// nastaveni parametru
		$params = array(
			"user" => array(
				"login" => "test",
				"password" => ""
			),
			"_sessid" => $session->hash_id
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		$this->assertAction("error");
	}
	
	public function testPostDataEmpty() {
		$this->_clearAll();
		
		$session = $this->_simulateSignIn(1);
		
		// nastaveni parametru
		$params = array(
			"_sessid" => $session->hash_id
		);
		
		// odelsani dotazu
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/post");
		
		$this->assertAction("error");
	}
	
	public function testPutCorrectSelf() {
		$this->_clearAll();
		
		// zaneseni testovaciho uzivatele
		$user = $this->createUser("test", "a");
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn($user->id);
		
		// pokus o zmenu informaci
		$params = array(
			"user" => array(
				"password" => "b"
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/put");
		
		$this->assertAction("put");
		
		$user = $this->_tableUsers->fetchRow(null, "id desc");
		$this->assertTrue($user->isPasswordSame($params["user"]["password"]));
	}
	
	public function testPutUnauthSelfAdmin() {
		$this->_clearAll();
		
		// zaneseni testovaciho uzivatele
		$user = $this->createUser("test", "a");
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn($user->id);
		
		// pokus o zmenu informaci
		$params = array(
			"user" => array(
				"is_admin" => 1
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/put");
		
		$this->assertAction("error");
	}
	
	public function testPutUnauthSelfBlock() {
		$this->_clearAll();
		
		// zaneseni testovaciho uzivatele
		$user = $this->createUser("test", "a");
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn($user->id);
		
		// pokus o zmenu informaci
		$params = array(
			"user" => array(
				"is_blocked" => 1
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/put");
		
		$this->assertAction("error");
	}
	
	public function testPutUnauthOther() {
		$this->_clearAll();
		
		// zaneseni testovaciho uzivatele
		$user = $this->createUser("test", "a");
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn($user->id);
		
		// pokus o zmenu informaci
		$params = array(
			"user" => array(
				"id" => 2,
				"is_admin" => 1
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/put");
		
		$this->assertAction("error");
	}
	
	public function testPutAuthOther() {
		$this->_clearAll();
		
		// zaneseni testovaciho uzivatele
		$user = $this->createUser("test", "a");
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn(1);
		
		// pokus o zmenu informaci
		$params = array(
			"user" => array(
				"id" => $user->id,
				"is_admin" => 1,
				"password" => "prdel",
				"is_blocked" => 1
			),
			"_sessid" => $session->hash_id
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/put");
		
		$this->assertAction("put");
		
		// kontrola novych dat
		$user->refresh();
		
		$this->assertEquals(1, $user->is_admin);
		$this->assertEquals(1, $user->is_blocked);
		$this->assertTrue($user->isPasswordSame("prdel"));
	}
	
	public function testSigninCorrect() {
		$this->_clearAll();
		
		// test zalogovani roota
		$params = array(
			"user" => array(
				"login" => "root",
				"password" => self::ROOT_PSW
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/signin");
		
		$this->assertAction("signin");
		$this->assertResponseCode(200);
		
		// kontrola existence session
		$sessions = $this->_tableSessions->fetchAll();
		$this->assertEquals(1, $sessions->count());
		
		// test chybneho zalogovani . spatne heslo
		$this->resetRequest();
		$this->resetResponse();
		$params["user"]["login"] = "root";
		$params["user"]["password"] = "a";
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/signin");
		$this->assertAction("error");
	}

	public function testSigninNotExists() {
		$this->_clearAll();
		
		// test zalogovani roota
		$params = array(
			"user" => array(
				"login" => "blbec",
				"password" => self::ROOT_PSW
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/signin");

		$this->assertEquals(500, $this->getResponse()->getHttpResponseCode());
	}
	
	public function testSigninWrongPassword() {
		$this->_clearAll();
		
		// test zalogovani roota
		$params = array(
			"user" => array(
				"login" => "root",
				"password" => "p"
			)
		);
		
		$this->getRequest()->setParams($params);
		$this->dispatch("/system/user/signin");

		$this->assertEquals(500, $this->getResponse()->getHttpResponseCode());
	}
	
	public function testSignoutRegular() {
		$this->_clearAll();
		
		// simulace prihlaseni
		$session = $this->_simulateSignIn(1);
		
		// pokus o odhlaseni
		$this->getRequest()->setParams(array("_sessid" => $session->hash_id));
		$this->dispatch("/system/user/signout");
		
		$this->assertAction("signout");
		$this->assertResponseCode(200);
	}
	
	public function testSignoutNobody() {
		$this->_clearAll();
		
		$this->dispatch("/system/user/signout");
		$this->assertResponseCode(200);
		$this->assertAction("signout");
	}
	
	/**
	 * @return Application_Model_Row_User
	 */
	protected function createUser($login, $password) {
		$user = $this->_tableUsers->createRow();
		
		$user->login = $login;
		$user->setPassword($password);
		$user->save();
		
		return $user;
	}
	
	protected function _simulateSignIn($id) {
		$session = $this->_tableSessions->createRow();
		$session->user_id = $id;
		$session->save();
		
		return $session;
	}
	
	protected function _clearAll() {
		$this->_tableSessions->getAdapter()->query("truncate table " . $this->_tableSessions->info("name"));
		$this->_tableUsers->delete("id > 2");
	}
}
