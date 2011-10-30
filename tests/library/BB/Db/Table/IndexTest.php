<?php
class Index extends BB_Db_Table_Index_Abstract {
	protected $_uuidColumns = array("uuid");
	
	protected $_name = "test_index";
	
	protected $_sequence = false;
	
	protected $_primary = "id";
	
	protected $_referenceMap = array(
		"uuid" => array(
			"columns" => "uuid",
			"refTableName" => "Application_Model_Data",
			"refColumns" => "uuid"
		)
	);
	
	public function indexRow(BB_Db_Table_Data_Row $row) {
		if ($this->isIndexed($row))
			return $this;
		
		$index = $this->createRow();
		$index->uuid = $row->getUuid();
		$index->id = $row->id;
		$index->save();
		
		return $this;
	}
	
	public function isIndexed(BB_Db_Table_Data_Row $row) {
		return ($this->find($row->id)->current()) ? true : false;
	}
}

class IndexTest extends Zend_Test_PHPUnit_ControllerTestCase {
	protected $_tableIndex;
	
	public function setUp() {
		$this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		parent::setUp();
		
		// vymazani databaze dat
		$this->_tableData = new Application_Model_Data();
		$this->_tableIndex = new Index;
		
		$this->_clearAll();
	}
	
	public function testTable() {
		// test skutecnych uuid
		$this->assertEquals(array("uuid"), $this->_tableIndex->getUuidCols());
		
		// test prelozenych uuid
		$this->assertEquals(array("`test_index`.`uuid`" => "tbl_test_index_uuid"), $this->_tableIndex->getTranslatedUuids());
		
		$this->_clearAll();
	}
	
	public function testRowset() {
		$row1 = $this->_insertRow(array("id" => 1), 1, "TEST");
		$row1->save(1);
		
		$row2 = $this->_insertRow(array("id" => 2), 1, "TEST");
		$row2->save(1);
		
		// zanseseni do indexu
		$this->_tableIndex->indexRow($row1)->indexRow($row2);
		
		// nacteni rowsetu
		$rowset = $this->_tableIndex->fetchAll();
		
		// kontrola uuid
		$uuidList = $rowset->getUuids();
		
		$this->assertTrue(in_array($row1->getUuid(), $uuidList));
		$this->assertTrue(in_array($row2->getUuid(), $uuidList));
		
		// nacteni objektu
		$objs = $rowset->getObjects($this->_tableData);
		$this->assertEquals(2, $objs->count());
		
		$this->_clearAll();
	}
	
	public function testRow() {
		$row1 = $this->_insertRow(array("id" => 1), 1, "TEST");
		$row1->save(1);
		
		$this->_tableIndex->indexRow($row1);
		$index = $this->_tableIndex->fetchAll()->current();
		
		$this->assertEquals($row1->getUuid(), $index->uuid);
		
		$row1a = $index->getObjects($this->_tableData)->current();
		
		$this->assertEquals($row1->getUuid(), $row1a->getUuid());
		
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
