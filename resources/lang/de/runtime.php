<?php

declare(strict_types=1);

return [
    'consent' => [
        'unknown_categories' => 'Eine oder mehrere Präferenzkategorien sind nicht erlaubt.',
        'invalid_preference' => 'Jede Präferenz muss ein Boolean-Wert sein.',
    ],

    'banner' => [
        'title' => 'Cookie-Einstellungen',
        'message' => 'Wir verwenden Cookies, damit die Website funktioniert und – mit Ihrer Zustimmung – um sie zu messen und zu verbessern. Sie können alle akzeptieren, optionale ablehnen oder Kategorien wählen.',
        'accept_all' => 'Alle akzeptieren',
        'reject_optional' => 'Optionale ablehnen',
        'customize' => 'Anpassen',
        'save' => 'Einstellungen speichern',
        'privacy' => 'Datenschutzerklärung',
        'error' => 'Einstellungen konnten nicht gespeichert werden. Bitte erneut versuchen.',
        'categories' => [
            'necessary' => [
                'label' => 'Notwendig',
                'description' => 'Erforderlich für Sicherheit, Speicherung der Einwilligung und grundlegende Funktionen. Immer aktiv.',
            ],
            'preferences' => [
                'label' => 'Präferenzen',
                'description' => 'Speichern Entscheidungen wie Sprache oder Anzeige.',
            ],
            'analytics' => [
                'label' => 'Analyse',
                'description' => 'Helfen zu verstehen, wie Besucher die Website nutzen, um sie zu verbessern.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Werden für personalisierte Inhalte und Kampagnenmessung verwendet.',
            ],
        ],
    ],
];
