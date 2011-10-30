<?php
class BB_Db_Table_Data_Abstract extends Zend_Db_Table_Abstract {
	const SQL_TIMESTAMP = "y-MM-dd HH:mm:ss";
	
	const SQL_TIMESTAMP_ZERO = "0000-00-00 00:00:00";
	
	protected $_rowClass = "BB_Db_Table_Data_Row";
	
	protected $_rowsetClass = "BB_Db_Table_Data_Rowset";
	
	public function createRow(array $data) {
		// vytvoreni radku
		$row = parent::createRow();
		$row->setContent($data);
		
		return $row;
	}
	
	public function deleteObjects(array $uuids, $objectType = null) {
		$adapter = $this->getAdapter();
		
		$where = $this->_generateWhere($uuids, $objectType);
		
		return parent::delete($where);
	}
	
	public function updateObjects(array $data, array $uuids, $userId, $objectType = null) {
		// prevod na JSON
		$content = Zend_Json::encode($data);
		
		$where = $this->_generateWhere($uuids, $objectType);
		
		// vygenerovani dat pro update
		$updateData = array(
			"content" => $content,
			"modified_user_id" => $userId,
			"modified_at" => new Zend_Db_Expr("NOW()")
		);
		
		// update
		return parent::update($updateData, $where);
	}
	
	protected function _generateWhere(array $uuids, $objectType) {
		$adapter = $this->getAdapter();
		
		if (!$uuids)
			$uuids = array(0);
		
		$where = array(
			$adapter->quoteInto("`uuid` in (?)", $uuids)
		);
		
		if ($objectType) {
			$where[] = $adapter->quoteInto("object_type like ?", $objectType);
		}
		
		return $where;
	}
}
