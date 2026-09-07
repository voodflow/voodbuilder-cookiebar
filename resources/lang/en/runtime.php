<?php

declare(strict_types=1);

return [
    'consent' => [
        'unknown_categories' => 'One or more preference categories are not allowed.',
        'invalid_preference' => 'Each preference must be a boolean value.',
    ],

    'banner' => [
        'title' => 'Cookie preferences',
        'message' => 'We use cookies to make this site work and, with your consent, to measure and improve it. You can accept all cookies, reject optional ones, or choose categories.',
        'accept_all' => 'Accept all',
        'reject_optional' => 'Reject optional',
        'customize' => 'Customize',
        'save' => 'Save preferences',
        'privacy' => 'Privacy policy',
        'cookie_policy' => 'Cookie policy',
        'error' => 'Could not save your preferences. Please try again.',
        'reopen' => 'Cookie preferences',
        'categories' => [
            'necessary' => [
                'label' => 'Necessary',
                'description' => 'Required for security, consent storage, and basic site features. Always on.',
            ],
            'preferences' => [
                'label' => 'Preferences',
                'description' => 'Remembers choices such as language or display settings.',
            ],
            'analytics' => [
                'label' => 'Analytics',
                'description' => 'Helps understand how visitors use the site so it can be improved.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Used for personalized content and campaign measurement.',
            ],
        ],
    ],
];
