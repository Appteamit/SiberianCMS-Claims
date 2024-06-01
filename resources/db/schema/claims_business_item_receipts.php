<?php

$schemas = $schemas ?? [];
$schemas['claims_business_item_receipts'] = [
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
            'name' => 'FK_CLAIMS_BUSINESS_RECEIPTS_CID',
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
    'claim_item_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'claims_business_item',
            'column' => 'id',
            'name' => 'FK_CLAIMS_BUSINESS_ITEM_RECEIPTS_CID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'claim_item_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'image' => [
        'type' => 'text',
        'is_null' => false
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];