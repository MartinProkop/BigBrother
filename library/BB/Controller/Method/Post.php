<?php
class BB_Controller_Method_Post extends BB_Controller_Method_Abstract {
	public function exeAction() {
		// nacteni dat
		$objType = strtolower($this->getDataObjectType());
		
		$data = $this->getRequest()->getParam($objType, array());
		$data = (array) $data;
		
		// vytvoreni radku a ulozeni dat
		$row = $this->createDataRow($data);
		$row->save($this->getUser()->id);
		
		// zapsani odpovedi
		$this->_responseObject->data = $row->toArray();
		$this->_responseObject->setOk();
	}
}
