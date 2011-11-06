<?php
abstract class BB_Controller_Method_Abstract extends BB_Controller_Abstract {
	const STD_RESPONSE_PATH = "views/scripts/std_response.phtml";
	
	/**
	 * obsahuje data, nad kterymi se bude provadet operace
	 * 
	 * @var BB_Db_Table_Data_Rowset
	 */
	private $_dataContainer;
	
	/**
	 * obsahuje informace potrebne pro nacteni dat
	 * 
	 * @var array|stdClass
	 */
	private $_queryObject;
	
	/**
	 * instance reprezentace tabulky dat
	 * 
	 * @var BB_Db_Table_Data_Abstract
	 */
	private $_tableData;
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		// zavolani konstruktoru predka
		parent::__construct($request, $response, $invokeArgs);
		
		// nastaveni tabulky
		$this->_tableData = new Application_Model_Data;
		
		/*
		 * nastaveni dat abstrakce
		 */
		
		// vytvoreni query objektu
		$this->_generateQuery();
		
		// nacteni dat
		$this->reloadData();
		
		// nastaveni vychoziho vystupu
		$this->view->setScriptPath(APPLICATION_PATH . "/views/scripts/");
		$this->_helper->viewRenderer->setRender("std-response", "standard", "default");
		$this->_responseObject->objectType = $this->getDataObjectType();
	}
	
	abstract public function exeAction();
	
	public function getContainer() {
		return $this->_dataContainer;
	}
	
	public function createDataRow($data) {
		/**
		 * @var BB_Db_Table_Data_Row
		 */
		$row = $this->_tableData->createRow($data);
		$row->setInitialData($this->getUser()->id, $this->getDataObjectType());
		
		return $row;
	}
	
	public function getDataObjectType() {
		return strtoupper($this->getRequest()->getModuleName());
	}
	
	public function getQuery() {
		return $this->_queryObject;
	}
	
	public function reloadData() {
		// prirpava seznamu UUID
		$uuids = array();
		
		// vyhodnoceni typu dotazu
		if (is_array($this->_queryObject)) {
			// queryObject obsahuje seznam UUID - objekt se slouci s predgenerovanymi hodnotami
			$uuids += $this->_queryObject;
		} elseif ($this->_queryObject instanceof stdClass) {
			// obsahuje data filtracniho objektu probehne sestaveni dotazu
			$where = $this->_queryObject->__toString();
			
			// sestaveni seznamu tabulek
			$tableList = array();
			$objName = strtolower($this->getDataObjectType()) . "_";
			
			foreach ($this->_queryObject->tables as $table) {
				$tableList[] = "`" . $objName . $table . "`";
			}
			
			$tables = implode(",", $tableList);
			
			// sestaveni seznamu sloupcu s UUID
			$columns = implode(",", $this->_queryObject->columns);
			
			// konecne sestaveni dotazu a nacteni dat
			$query = "SELECT " . $columns . " FROM " . $tables . " WHERE " . $where;
			
			/**
			 * @var Zend_Db_Adapter_Abstract
			 */
			$adapter = $this->_tableData()->getAdapter();
			
			/**
			 * @var Zend_Db_Statement_Interface
			 */
			$result = $adapter->query($query);
			$result->setFetchMode(PDO::FETCH_NUM);
			
			// ziskani seznamu UUID
			$uuidList = array();
			
			while ($record = $result->fetch()) {
				$uuidList += $record;
			}
			
			// slouceni se seznamem
			$uuids += $uuidList;
		}

		// kontrola prazdnosti seznamu
		if (empty($uuids)) {
			// seznam je prazdny, vytvori se prazdny rowset
			$config = array(
				"table" => $this->_tableData,
				"rowClass" => "BB_Db_Table_Data_Row"
			);
			
			$this->_dataContainer = new BB_Db_Table_Data_Rowset($config);
			
			return $this;
		}
		
		// nacteni dat
		$adapter = $this->_tableData->getAdapter();
		
		$condition = array(
			$adapter->quoteInto("object_type like ?", $this->getDataObjectType()),
			$adapter->quoteInto("uuid in (?)", $uuids)
		);
		
		// nacteni dat
		$this->_dataContainer = $this->_tableData->fetchAll($condition);
		
		return $this;
	}
	
	/**
	 * vytvori a zapise dotazovaci objekt
	 */
	private function _generateQuery() {
		// vyhodnoceni odeslanych filtracnich dat
		$filterObj = $this->getRequest()->getParam("_filter", null);
		$uuid = $this->getRequest()->getParam("id", null);
		$uuids = $this->getRequest()->getParam("_uuids", null);
		
		// vyhodnoceni stavu
		if ($uuid) {
			// uuid byl odeslan primo v requestu
			$this->_queryObject = array($uuid);
			
			return;
		} elseif ($uuids) {
			// byl odeslan seznam uuid
			$this->_queryObject = (array) $uuids;
			
			return;
		} elseif (!$filterObj) {
			// zadna z moznosti nebyla vyuzita
			$this->_queryObject = array();
		}
		
		/**
		 * pokud program dosel az sem, byl odeslan plnohodnotny filtracni objekt
		 */ 
		
		// reset referenci
		BB_Db_Query_Reference::clearTables();
		
		// vytvoreni filtracniho objektu a ziskani seznamu tabulek
		$queryObj = BB_Db_Query_Factory::factory($filterObj);
		$tableNames = BB_Db_Query_Reference::getTables();
		
		// vygenerovani seznamu pouzitych sloupcu z index
		$usedColumns = $this->getRequest()->getParam("_uuidColumns", array());
		$usedColumns = (array) $usedColumns;
		
		if (!$usedColumns) {
			// seznam pouzitych sloupci je prazdny - toto neni pripustne
			throw new Zend_Exception("UUID_COLUMNS_NOT_SET", 400);
		}
		
		$usedReferences = array();
		
		foreach ($usedColumns as $column) {
			$reference = new BB_Db_Query_Reference($column);
			
			// kontrola jestli je tabulka v seznamu
			if (!in_array($reference->getTable(), $tableNames)) {
				// tabulka neni v seznamu, vyhodi se chyba
				throw new Zend_Exception("UNKNOWN_INDEX_UUID_COLUMN", 400);
			}
			
			$usedReferences[] = new BB_Db_Query_Reference($column);
		}
		
		// anulace statickych vlastnosti reference
		BB_Db_Query_Reference::clearTables();
		
		// vygenerovani infomraci pro filtraci dat
		$filterData = new stdClass;
		
		$filterData->columns = $usedReferences;
		$filterData->query = $queryObj;
		$filterData->tables = $tableNames;
		
		// nastaveni objektu
		$this->_queryObject = $filterData;
		
		return $this;
	}
}
