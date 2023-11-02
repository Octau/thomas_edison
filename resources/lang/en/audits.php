<?php

return [
    'created' => [
        'message' => ':attribute has been created <strong>:new</strong>',
    ],
    'updated'            => [
        'metadata' => 'On :audit_created_at, :user_email [:audit_ip_address] updated this record via :audit_url',
        'message' => ':attribute has been modified from <strong>:old</strong> to <strong>:new</strong>',
    ],
    'deleted' => [
        'message' => ':attribute has been deleted',
    ],
];
