<?php
return [
    'guest' => [
        'type' => 1,
    ],
    'user' => [
        'type' => 1,
        'children' => [
            'update-own-pet',
            'update-own-profile',
        ],
    ],
    'admin' => [
        'type' => 1,
    ],
    'banned' => [
        'type' => 1,
    ],
    'update-own-pet' => [
        'type' => 2,
        'ruleName' => 'isAuthor',
    ],
    'update-own-profile' => [
        'type' => 2,
        'ruleName' => 'isOwnProfile',
    ],
];
