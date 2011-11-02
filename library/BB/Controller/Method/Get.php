<?php
class BB_Controller_Method_Get extends BB_Controller_Method_Abstract {
	public function exeAction() {
		$this->_responseObject->data = $this->getContainer()->toArray();
		$this->_responseObject->setOk();
	}
}
