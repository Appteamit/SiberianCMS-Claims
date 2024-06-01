<?php

/**
 * Class Claims_Model_Db_Table_Business
 */
class Claims_Model_Db_Table_Business extends Core_Model_Db_Table
{
    /**
     * @var string
     */
    protected $_name = 'claims_business';

    /**
     * @var string
     */
    protected $_primary = 'id';

    /**
     * @param $customerId
     * @param array $params
     * @return array
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function getAllClaims($customerId, $params = [])
    {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "customer_id",
                "value_id",
                "status_id",
                "title",
                "location",
                "start_date",
                "end_date",
                "paid_amount",
                "amount",
                "remaining_amount",
                "created_at",
            ]);

        $select->where("main.customer_id = ?", $customerId);
        $select->joinLeft(['s' => 'claims_status'], 's.id = main.status_id', ['s.status_name']);
        $select->order('main.created_at DESC');


        return $this->_db->fetchAll($select);
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
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "customer_id",
                "value_id",
                "status_id",
                "title",
                "location",
                "start_date",
                "end_date",
                "paid_amount",
                "amount",
                "remaining_amount",
                "created_at",
            ]);

        $select->where("main.value_id = ?", $value_id);

        $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.customer_id as customerId', 'c.email', 'c.firstname', 'c.lastname', 'c.image']);

        $select->joinLeft(['s' => 'claims_status'], 's.id = main.status_id', ['s.status_name']);
        $select->where("s.status_name != ?", 'Not Submitted');

        if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
            $select->limit($params["limit"], $params["offset"]);
        }

        if (array_key_exists("filter", $params)) {
            $select->where("(c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ?)", "%" . $params["filter"] . "%");
        }

        if (array_key_exists("status_id", $params) && !empty($params["status_id"])) {
            $select->where("main.status_id  = ?", $params["status_id"]);
        }

        if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
            $orders = [];
            foreach ($params["sorts"] as $key => $dir) {
                $order = ($dir == -1) ? "DESC" : "ASC";
                if ($key == 'firstname') {
                    $orders = "c.{$key} {$order}";
                } else {
                    $orders = "main.{$key} {$order}";
                }
            }
            $select->order($orders);
        } else {
            $select->order('main.id DESC');
        }

        if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
            $select->where("main.created_at >= ?", $params["startdate"] . ' 00:00:00');
        }

        if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
            $select->where("main.created_at <= ?", $params["enddate"] . ' 23:59:59');
        }

        return $this->toModelClass($this->_db->fetchAll($select));

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
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                'COUNT(main.id)'
            ])
            ->where('main.value_id = ?', $value_id);

        $select->joinLeft(['s' => 'claims_status'], 's.id = main.status_id', ['s.status_name']);
        $select->where("s.status_name != ?", 'Not Submitted');
        $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.customer_id as customerId', 'c.email', 'c.firstname', 'c.lastname', 'c.image']);

        if (array_key_exists("filter", $params)) {
            $select->where("(c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ?)", "%" . $params["filter"] . "%");
        }

        if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
            $select->where("main.created_at >= ?", $params["startdate"]);
        }

        if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
            $select->where("main.created_at <= ?", $params["enddate"]);
        }

        if (array_key_exists("status_id", $params) && !empty($params["status_id"])) {
            $select->where("main.status_id = ?", $params["status_id"]);
        }

        return $this->_db->fetchCol($select);
    }

}