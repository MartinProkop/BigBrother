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
		$this->_helper->viewRenderer->renderScript(APPLICATION_PATH . "/views/scripts/std_response.phtml");
	}
	
	abstract public function exeAction();
	
	public function getContainer() {
		return $this->_dataContainer;
	}
	
	public function getDataObjectType() {
		return $this->getRequest()->getModuleName();
	}
	
	public function getQuery() {
		return $this->_queryObject;
	}
	
	public function reloadData() {
		// vyhodnoceni typu dotazu
		if (is_array($this->_queryObject)) {
			// queryObject obsahuje seznam UUID
			if (empty($this->_queryObject)) {
				// zadna data nejsou pozadovana
				$config = array(
					"table" => $this->_tableData,
					"rowClass" => "BB_Db_Table_Data_Row"
				);
				
				$this->_dataContainer = new BB_Db_Table_Data_Rowset($config);
				
				// neni potreba nic dalsiho delat, funkce se ukonci
				return $this;
			}
			
			// nacteni dat
			$this->_dataContainer = $this->_tableData->find($this->_queryObject);
			
			return $this;
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
			
			// nacteni dat
			$this->_dataContainer = $this->_tableData->find($uuidList);
			
			return $this;
		}

		// pokud se program dostal az sem, nebyl nalezen podporovany format dotazu
		throw new BB_Exception_ValidateError;
	}
	
	/**
	 * vytvori a zapise dotazovaci objekt
	 */
	private function _generateQuery() {
		// vyhodnoceni odeslanych filtracnich dat
		$filterObj = $this->getRequest()->getParam("_filter", null);
		$uuid = $this->getRequest()->getParam("_uuid", null);
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
