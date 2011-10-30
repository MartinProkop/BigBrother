<?php
require_once APPLICATION_PATH . "/models/Data.php";

class Application_Model_DataTest extends Zend_Test_PHPUnit_ControllerTestCase {
	public function setUp() {
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		
		// vymazani databaze dat
		$this->_tableData = new Application_Model_Data();
		
		$this->_clearAll();
	}
	
	public function testInsert() {
		// vytvoreni strany
		$data = array("test" => "test");
		
		$row = $this->_tableData->createRow($data);
		$row->setInitialData(1, "TEST");
		
		$row->save(1);
		
		// test UUID a ostatnich obecnych vlastnosti
		$this->assertNotNull($row->getUuid());
		$this->assertEquals(1, $row->getUser());
		$this->assertEquals("TEST", $row->getObjectType());
		$this->assertInstanceOf("Zend_Date", $row->getCreated());
		$this->assertInstanceOf("Zend_Date", $row->getModified());
		$this->assertEquals($data, $row->getContent());
		
		$this->_clearAll();
	}
	
	public function testUpdateRow() {
		// vlozeni testovacich dat
		$oldData = array("bar" => "foo");
		$this->_insertRow($oldData, 1, "TEST")->save(1);
		
		// nalezeni novych dat
		$row = $this->_tableData->fetchAll()->current();
		
		// test existence hodnoty
		$this->assertTrue(isset($row->bar));
		$this->assertFalse(isset($row->foo));
		
		// vraceni prazdneho setu
		$row->getEmptyRowset();
		
		$value = "Hello world!";
		$oldTS = $row->getModified()->get(BB_Db_Table_Data_Abstract::SQL_TIMESTAMP);
		sleep(2);
		$row->bar = $value;
		$row->save(2);
		
		// znovu nacteni dat
		$row = $this->_tableData->fetchAll()->current();
		$newTS = $row->getModified()->get(BB_Db_Table_Data_Abstract::SQL_TIMESTAMP);
		
		$this->assertEquals($value, $row->bar);
		$this->assertNotEquals($oldTS, $newTS);
		
		// kontrola zmeny uzivatele
		$this->assertEquals(2, $row->getLastUser());
		
		// kontorla ulozeni bez zadne zmeny
		$row->save(2);
		$this->assertEquals($row->getUuid(), $row->save(2));
		
		// kontrola znovunacneni hodnoty
		$row->foo = 3;
		$row->refresh();
		
		$this->assertFalse(isset($row->foo));
		
		$this->_clearAll();
	}
	
	public function testUpdateRowset() {
		// zapsani dvou radku
		$this->_insertRow(array("hodnota" => 1), 1, "TEST")->save(1);
		sleep(1);
		$this->_insertRow(array("hodnota" => 2), 1, "TEST")->save(1);
		
		// vyhledani dvou radku
		$rowset = $this->_tableData->fetchAll(null, "created_at");
		
		// kontrola typu
		$this->assertInstanceOf("BB_Db_Table_Data_Rowset", $rowset);
		
		// kontrola __GET
		$this->assertEquals(1, $rowset->hodnota);
		$rowset->next();
		
		$this->assertEquals(2, $rowset->hodnota);
		
		// kontrola __SET
		$rowset->hodnota = 3;
		
		// kontrola nastaveni
		$rowset->rewind();
		
		$this->assertEquals(3, $rowset->hodnota);
		$rowset->next();
		$this->assertEquals(3, $rowset->hodnota);
		
		// ulozeni
		$uuids = array($rowset[0]->getUuid(), $rowset[1]->getUuid());
		
		$this->assertEquals($uuids, $rowset->save(2));
		
		// zameteni stop
		$this->_clearAll();
	}
	
	public function testUpdateTable() {
		// vytvoreni radku a update pres tabulku
		$row = $this->_insertRow(array("hodnota" => 1), 1, "TEST");
		$row->save(1);
		
		$affected = $this->_tableData->updateObjects(array("hodnota" => 2), array($row->getUuid()), 1, "TEST");
		$this->assertEquals(1, $affected);
		
		// obnoveni dat a kontrola zmeny
		$row->refresh();
		
		$this->assertEquals(2, $row->hodnota);
		
		$this->_clearAll();
	}
	
	public function testDeleteTable() {
		// vytvoreni dat
		$row = $this->_insertRow(array("hodnota" => 1), 1, "TEST");
		$row->save(1);
		$this->_insertRow(array("hodnota" => 2), 1, "TEST")->save(1);
		
		// smazani dat
		$this->_tableData->deleteObjects(array($row->getUuid()));
		
		// kontorla smazani
		$rows = $this->_tableData->fetchAll();
		
		$this->assertEquals(1, $rows->count());
		
		$this->_clearAll();
	}
	
	protected function _insertRow($data, $userId, $objectType) {
		return $this->_tableData->createRow($data)->setInitialData($userId, $objectType);
	}
	
	protected function _clearAll() {
		$adapter = $this->_tableData->getAdapter();
		$adapter->query("truncate table " . $this->_tableData->info("name"));
	}
}
