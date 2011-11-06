<?php

class BB_Controller_Method_GetTest extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * @var BB_Db_Table_Data_Abstract
     */
    protected $_tableData;
    /**
     * @var Application_Model_Sessions;
     */
    protected $_tableSessions;

    public function setUp() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();

        $this->_tableData = new Application_Model_Data;
        $this->_tableSessions = new Application_Model_Sessions;
    }

    public function testGetRoot() {
        // simulace prihlaseni jako root
        $session = $this->_simulateSignIn(1);

        // zapsani testovacich dat
        $row = $this->_insertRow(array("id" => 1), 1, "TEST");

        // nastaveni parametru
        $params = array("_sessid" => $session->hash_id);
        $this->getRequest()->setParams($params);

        // vygenerovani dotazu
        $url = "/test/get/" . $row->getUuid();

        $this->dispatch($url);
        
        // kontola odpovedi
        $response = Zend_Json::decode($this->getResponse()->getBody());
        
        $this->assertEquals(1, count($response["data"]));
        $this->assertEquals(1, $response["data"][0]["content"]["id"]);
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

    protected function _insertRow($data, $userId, $objType) {
        /**
         * @var BB_Db_Table_Data_Row
         */
        $row = $this->_tableData->createRow($data);
        $row->setInitialData($userId, $objType);
        $row->save($userId);

        return $row;
    }

    public function tearDown() {
        $this->_tableData->delete("1");
    }

}
