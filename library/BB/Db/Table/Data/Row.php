<?php
class BB_Db_Table_Data_Row extends Zend_Db_Table_Row_Abstract {
	const MODIFIED_COLUMN = "modified_at";
	
	const CONTENT_COLUMN = "content";
	
	const CREATED_COLUMN = "created_at";
	
	const USER_COLUMN = "user_id";
	
	const OBJECT_COLUMN = "object_type";
	
	const USER_MODIFIED_COLUMN = "modified_user_id";
	
	const UUID_COLUMN = "uuid";
	
	protected $_created;
	
	protected $_modified;
	
	protected $_content = null;
	
	public function __construct(array $config = array()) {
		// provedeni materske deklarace
		parent::__construct($config);
		
		// nastaveni TS zmeny a vytvoreni
		$this->_modified = new Zend_Date($this->_data[self::MODIFIED_COLUMN], BB_Db_Table_Data_Abstract::SQL_TIMESTAMP);
		$this->_created = new Zend_Date($this->_data[self::CREATED_COLUMN], BB_Db_Table_Data_Abstract::SQL_TIMESTAMP);
	}
	
	public function __get($name) {
		$this->_parseContent();
		
		return isset($this->_content[$name]) ? $this->_content[$name] : null;
	}
	
	public function __set($name, $value) {
		$this->_parseContent();
		
		// zapis hodnoty
		$this->_content[$name] = $value;
		
		// oznaceni hodnoty jako zmenene
		$this->_modifiedFields[self::CONTENT_COLUMN] = true;
		
		return $value;
	}
	
	public function __isset($name) {
		$this->_parseContent();
		
		return isset($this->_content[$name]);
	}
	
	public function getContent() {
		$this->_parseContent();
		
		return $this->_content;
	}
	
	public function getCreated() {
		return $this->_created;
	}
	
	public function getEmptyRowset() {
		
	}
	
	public function getLastUser() {
		return $this->_data["modified_user_id"];
	}
	
	public function getModified() {
		return $this->_modified;
	}
	
	public function getObjectType() {
		return $this->_data[self::OBJECT_COLUMN];
	}
	
	public function getUser() {
		return $this->_data["user_id"];
	}
	
	public function getUuid() {
		return $this->_data[self::UUID_COLUMN];
	}
	
	public function refresh() {
		// anulace prelozeneho obsahu
		$this->_content = null;
		
		// obnova dat
		return parent::refresh();
	}
	
	public function save($userId) {
		// pokud se jedna o novy radek, vygeneruje se uuid
		if (empty($this->_cleanData)) {
			// ziskani reference na tabulku
			$table = $this->_table;
			
			// vygenerovani prvniho UUID, dokud je nalezen sloupec se stejnym UUID
			$uuid = sha1(time() . microtime() . $userId . $this->_data[self::OBJECT_COLUMN]);
			
			do {
				$uuid = sha1($uuid . microtime());
				$row = $table->find($uuid);
			} while ($row->count());
			
			// nastaveni sloupcu
			$this->_data[self::UUID_COLUMN] = $uuid;
			$this->_modifiedFields[self::UUID_COLUMN] = true;
		}
		
		// kontrola, jestli je modifikovan obsah
		if (!empty($this->_modifiedFields[self::CONTENT_COLUMN])) {
			// prevod dat do JSON
			parent::__set(self::CONTENT_COLUMN, Zend_Json::encode($this->_content));
			
			// nastaveni posledni zmeny
			$this->_modified = new Zend_Date();
			
			// zapsani infomraci o zmene
			$this->_data[self::MODIFIED_COLUMN] = $this->_modified->get(BB_Db_Table_Data_Abstract::SQL_TIMESTAMP);
			$this->_modifiedFields[self::MODIFIED_COLUMN] = true;
			
			$this->_data[self::USER_MODIFIED_COLUMN] = $userId;
			$this->_modifiedFields[self::USER_MODIFIED_COLUMN] = true;
			
			$retVal = parent::save();
			
			$this->_modifiedFields = array();
			
			return $retVal;
		}
		
		return $this->_data[self::UUID_COLUMN];
	}

	public function setContent(array $data) {
		$this->_content = $data;
		$this->_modifiedFields[self::CONTENT_COLUMN] = true;
		
		return $this;
	}

	public function setInitialData($userId, $objectType) {
		// nastaveni dat
		$this->_data["user_id"] = $userId;
		$this->_data["object_type"] = $objectType;
		
		// oznaceni zmen
		$this->_modifiedFields["user_id"] = true;
		$this->_modifiedFields["object_type"] = true;
		
		return $this;
	}
	
	private function _parseContent() {
		if (is_null($this->_content))
			$this->_content = Zend_Json::decode($this->_data[self::CONTENT_COLUMN], Zend_Json::TYPE_ARRAY);
	}
}
