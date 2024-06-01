<?php

$schemas = $schemas ?? [];
$schemas['claims_business_item'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'claim_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'claims_business',
            'column' => 'id',
            'name' => 'FK_CLAIMS_BUSINESS_ITEM_CID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'claim_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'category_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'claims_category',
            'column' => 'id',
            'name' => 'FK_CLAIMS_BUSINESS_ITEM_CATEGORY_CID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'category_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'amount' => [
        'type' => 'double',
        'is_null' => false,
        'default' => '0'
    ],
    'expense_date' => [
        'type' => 'varchar(100)',
        'is_null' => true
    ],
    'remark' => [
        'type' => 'text',
        'is_null' => true
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];