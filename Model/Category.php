<?php

/**
 * Class Claims_Model_Category
 * @package Claims\Model
 */
class Claims_Model_Category extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Category::class;

}