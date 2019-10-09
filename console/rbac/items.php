<?php
return [
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'error' => [
        'type' => 2,
    ],
    'sign-up' => [
        'type' => 2,
    ],
    'add-pet' => [
        'type' => 2,
    ],
    'update-pet' => [
        'type' => 2,
    ],
    'delete-pet' => [
        'type' => 2,
    ],
    'set-status' => [
        'type' => 2,
    ],
    'change-category' => [
        'type' => 2,
    ],
    'change-user' => [
        'type' => 2,
    ],
    'ban-user' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'children' => [
            'login',
            'logout',
            'error',
            'sign-up',
        ],
    ],
    'user' => [
        'type' => 1,
        'children' => [
            'add-pet',
            'delete-pet',
            'set-status',
            'update-own-pet',
            'update-own-profile',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'change-category',
            'update-pet',
            'change-user',
            'ban-user',
            'user',
        ],
    ],
    'banned' => [
        'type' => 1,
        'children' => [
            'error',
        ],
    ],
    'update-own-pet' => [
        'type' => 2,
        'children' => [
            'update-pet',
        ],
    ],
    'update-own-profile' => [
        'type' => 2,
        'children' => [
            'change-user',
        ],
    ],
];
