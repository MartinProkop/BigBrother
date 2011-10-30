<?php
interface BB_Db_Query_Interface {
	public function __construct($data);
	
	public function toArray($recursive = true);
	
	public function __toString();
}
