<?php

$schemas = $schemas ?? [];
$schemas['claims_business'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'FK_CLAIMS_BUSINESS_VID_AOV_VID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'status_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'claims_status',
            'column' => 'id',
            'name' => 'FK_CLAIMS_BUSINESS_STATUS_SID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'status_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'customer',
            'column' => 'customer_id',
            'name' => 'FK_CLAIMS_BUSINESS_CUSTOMER_ID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'customer_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'title' => [
        'type' => 'varchar(255)',
        'is_null' => false
    ],
    'location' => [
        'type' => 'varchar(255)',
        'is_null' => true
    ],
    'start_date' => [
        'type' => 'varchar(100)',
        'is_null' => true
    ],
    'end_date' => [
        'type' => 'varchar(100)',
        'is_null' => true
    ],
    'amount' => [
        'type' => 'double',
        'is_null' => false,
        'default' => '0'
    ],
    'paid_amount' => [
        'type' => 'double',
        'is_null' => true,
        'default' => '0'
    ],
    'remaining_amount' => [
        'type' => 'double',
        'is_null' => true,
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];