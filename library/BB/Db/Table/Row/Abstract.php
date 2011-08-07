<?php

abstract class BB_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract {

    /**
     * vrati hodnotu sloupce
     *
     * @param string $key dotazovany sloupec
     * @return mixed
     */
    public function __get($key) {
        $row = $this->_row;

        if (!$row)
            return null;

        return $row->$key;
    }

    /**
     * nastavi hodnotu sloupce
     *
     * @param string $key jmeno sloupce
     * @param mixed $value nova hodnota
     * @return void
     */
    public function __set($key, $value) {
        $row = $this->_row;

        $row->$key = $value;

        return $value;
    }

    /**
     * ulozi zmeny ve sloupci
     *
     * @return BB_Db_Table_Row_Abstract
     */
    public function save() {
        $row = $this->_row;

        $row->save();

        return $this;
    }

    /**
     * smaze radek
     *
     * @return BB_Db_Table_Row_Abstract
     */
    public function delete() {
        $row = $this->_row;

        $row->delete();

        return $this;
    }

    /**
     * obnovi data v radku
     *
     * @return BB_Db_Table_Row_Abstract
     */
    public function refresh() {
        $row = $this->_row;

        $row->refresh();

        return $this;
    }

}
?>
