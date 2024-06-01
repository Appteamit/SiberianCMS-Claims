<?php

/**
 * Class Claims_Model_Business
 */
class Claims_Model_Business extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Business::class;

    /**
     * @param $customerId
     * @param null $param
     * @return array
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function getAllClaims($customerId, $param = null)
    {
        return $this->getTable()->getAllClaims($customerId, $param);
    }

    /**
     * @param $value_id
     * @param array $params
     * @return mixed
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Statement_Exception
     * @throws Zend_Exception
     */
    public function findAllForApp($value_id, $params = [])
    {
        return $this->getTable()->findAllForApp($value_id, $params);
    }

    /**
     * @param $value_id
     * @param array $params
     * @return array
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function countAllForApp($value_id, $params = [])
    {
        return $this->getTable()->countAllForApp($value_id, $params);
    }

}