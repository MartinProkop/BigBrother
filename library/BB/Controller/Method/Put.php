<?php
class BB_Controller_Method_Put extends BB_Controller_Method_Abstract {
	public function exeAction() {
		// nacteni dat
		$objType = strtolower($this->getDataObjectType(), array());
		$objType = (array) $objType;
		
		// nacteni rosetu
		$rowset = $this->getContainer();
		
		// update dat
		foreach ($objType as $key => $value)
			$rowset->__set($key, $value);
		
		$rowset->save();
		
		$this->_responseObject->data = $rowset->toArray();
		$this->_responseObject->setOk();
	}
}
