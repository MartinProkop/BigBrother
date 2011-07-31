<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function run() {
		Zend_Loader_Autoloader::getInstance()->registerNamespace("BB_");
		
		parent::run();
	}

}

