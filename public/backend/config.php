<?php

return [
    'CONNECTION' => [
        'MYSQL' => [
            'adapter'     => 'Mysql',
            'host'        => 'mariadb',
            'username'    => 'user',
            'password'    => 'secret',
            'dbname'      => 'db',
            'charset'     => 'utf8',
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
