<?php

/**
 * Class Claims_Model_Item
 * @package Claims\Model
 */
class Claims_Model_Item extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Item::class;

    /**
     * @param $claim_id
     * @return mixed
     */
    public function itemById($claim_id, $param = null)
    {
        return $this->getTable()->itemById($claim_id, $param);
    }

}