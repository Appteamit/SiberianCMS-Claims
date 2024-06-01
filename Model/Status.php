<?php

/**
 * Class Claims_Model_Status
 * @package Claims\Model
 */
class Claims_Model_Status extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;


    /**
     * @var string
     */
    protected $_db_table = Claims_Model_Db_Table_Status::class;

    /**
     * @return array
     */
    public function defaultStatus(): array
    {
        return [
            ['name' => 'Not Submitted', 'key' => 'not-submit', 'is_default' => 0],
            ['name' => 'Reviewing', 'key' => 'reviewing', 'is_default' => 1],
            ['name' => 'Accepted', 'key' => 'paid', 'is_default' => 0],
            ['name' => 'Cancelled', 'key' => 'cancelled', 'is_default' => 0],
            ['name' => 'Rejected', 'key' => 'rejected', 'is_default' => 0],
            ['name' => 'Paid', 'key' => 'paid', 'is_default' => 0],
            ['name' => 'Partially Reimbursed', 'key' => 'partially-reimbursed', 'is_default' => 0]
        ];
    }


}