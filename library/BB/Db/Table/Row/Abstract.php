<?php

abstract class BB_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract {

    protected $position = 0;

    public function __construct() {
        $this->position = 0;
    }

    /**
     * vraci hodnotu sloupce, na ktery ukazuje ukazatel iteratoru
     *
     * @return mixed
     */
    public function current() {
        $row = $this->_data;
        return $row[$this->position];
    }

    /**
     * vrati nazev sloupce na ktery ukazuje ukazatel
     *
     * @return scalar
     */
    public function key() {
        $row = $this->_data;
        $keys = array_keys($this->position);
        return $keys[$this->position];
    }

    /**
     * posune ukazatel
     *
     * @return void
     */
    public function next() {
        ++$this->position;
    }

    /**
     * vraci ukazatel na začátek
     *
     * @return void
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * ověřuje, že existuje sloupec
     *
     * @return boolean true kdyz pozice existuje jinak false
     */
    public function valid() {
        $row = $this->_data;
        return isset($row[$this->position]);
    }

}
?>
