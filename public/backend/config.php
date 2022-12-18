<?php

return [
    'CONNECTION' => [
        'DEFAULT' => 'MYSQL',
        'MYSQL' => [
            'host'        => 'mariadb',
            'username'    => 'user',
            'password'    => 'secret',
            'dbname'      => 'db',
            'charset'     => 'utf8',
        ],
        'MONGO' => [
            'host'        => 'mongodb',
            'dbname'      => 'db'
        ]
    ],
    'MESSAGES' => [
        'INSERT' => [
            'success' => 'Record successfully inserted',
            'error' => 'Failed to insert'
        ],

        'UPDATE' => [
            'success' => 'Record successfully modified',
            'error' => 'Failed to Modify',
        ],

        'DELETE' => [
            'success' => 'Record successfully deleted',
            'error' => 'Failed to delete',
        ],

        'CONNECTION' => [
            'noRecords' => 'No records found',
            'error' => 'You do not have permission to consult'
        ]
    ]
];
