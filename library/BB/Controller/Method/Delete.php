<?php
class BB_Controller_Method_Delete extends BB_Controller_Method_Abstract {
	public function exeAction() {
		$this->getContainer()->delete();

		$this->_responseObject->setOk();
	}
}
