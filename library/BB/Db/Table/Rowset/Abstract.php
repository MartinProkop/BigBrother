<?php

abstract class BB_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract {

    /**
     * vraci hodnotu sloupce, na ktery ukazuje ukazatel iteratoru
     *
     * @param string $key dotazovany sloupec
     * @return mixed
     */
    public function __get($key) {
        $row = $this->current();

        if (!$row)
            return null;

        return $row->$key;
    }

    /**
     * nastavi hodnotu sloupce u vsech radku
     * 
     * @param string $key jmeno sloupce
     * @param mixed $value nova hodnota
     * @return void
     */
    public function __set($key, $value) {
        foreach ($this->_rows as $row) {
            $row->$key = $value;
        }

        return $value;
    }

    /**
     * ulozi zmeny ve vsech sloupcich v setu
     * 
     * @return BB_Db_Table_Rowset_Abstract
     */
    public function save() {
        foreach ($this->_rows as $row) {
            $row->save();
        }

        return $this;
    }

    /**
     * smaze vsechny  radky v setu
     * 
     * @return BB_Db_Table_Rowset_Abstract
     */
    public function delete() {
        foreach ($this->_rows as $row) {
            $row->delete();
        }

        return $this;
    }

    /**
     * obnovi data ve vsech radcich
     * 
     * @return BB_Db_Table_Rowset_Abstract
     */
    public function refresh() {
        foreach ($this->_rows as $row) {
            $row->refresh();
        }

        return $this;
    }

}
?>
