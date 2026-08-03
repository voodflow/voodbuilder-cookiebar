<?php

declare(strict_types=1);

return [
    'consent' => [
        'unknown_categories' => 'Une ou plusieurs catégories de préférence ne sont pas autorisées.',
        'invalid_preference' => 'Chaque préférence doit être une valeur booléenne.',
    ],

    'banner' => [
        'title' => 'Préférences cookies',
        'message' => 'Nous utilisons des cookies pour faire fonctionner le site et, avec votre consentement, pour le mesurer et l’améliorer. Vous pouvez tout accepter, refuser les cookies optionnels ou choisir les catégories.',
        'accept_all' => 'Tout accepter',
        'reject_optional' => 'Refuser les optionnels',
        'customize' => 'Personnaliser',
        'save' => 'Enregistrer',
        'privacy' => 'Politique de confidentialité',
        'cookie_policy' => 'Cookie policy',
        'reopen' => 'Cookie preferences',
        'error' => 'Impossible d’enregistrer vos préférences. Réessayez.',
        'categories' => [
            'necessary' => [
                'label' => 'Nécessaires',
                'description' => 'Requis pour la sécurité, le stockage du consentement et les fonctions de base. Toujours actifs.',
            ],
            'preferences' => [
                'label' => 'Préférences',
                'description' => 'Mémorisent des choix comme la langue ou l’affichage.',
            ],
            'analytics' => [
                'label' => 'Analytique',
                'description' => 'Aident à comprendre comment les visiteurs utilisent le site pour l’améliorer.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Utilisés pour du contenu personnalisé et la mesure des campagnes.',
            ],
        ],
    ],
];
