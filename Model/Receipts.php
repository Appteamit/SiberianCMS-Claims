<?php

/**
 * Class Claims_Model_Receipts
 * @package Claims\Model
 */
class Claims_Model_Receipts extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Receipts::class;

}