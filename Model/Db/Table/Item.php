<?php

/**
 * Class Claims_Model_Db_Table_Item
 */
class Claims_Model_Db_Table_Item extends Core_Model_Db_Table
{
    /**
     * @var string
     */
    protected $_name = 'claims_business_item';

    /**
     * @var string
     */
    protected $_primary = 'id';

    /**
     * @param $claim_id
     * @return array
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function itemById($claim_id): array
    {

        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                'id as item_id',
                "category_id",
                "amount",
                "expense_date",
                "remark",
                "created_at",
                'receipts' => new Zend_Db_Expr('(' . $this->_db->select()->from(array('r' => 'claims_business_item_receipts'), array(new Zend_Db_Expr('COUNT(r.id)')))->where('r.claim_item_id = main.id') . ')')
            ]);

        $select->joinLeft(['c' => 'claims_category'], 'c.id = main.category_id', ['c.category_name']);
        $select->where("main.claim_id = ?", $claim_id);
        $select->order(["created_at DESC"]);


        return $this->_db->fetchAssoc($select);
    }

}