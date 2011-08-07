<?php

class BB_Db_Table extends Zend_Db_Table_Abstract {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'BB_Db_Table_Row';
    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'BB_Db_Table_Rowset';


    public function __construct(array $data = null) {
        if ($data)
            $this->set($data);
    }

}
?>
