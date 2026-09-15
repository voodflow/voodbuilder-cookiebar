<?php

declare(strict_types=1);

return [
    'consent' => [
        'unknown_categories' => 'Una o più categorie di preferenza non sono consentite.',
        'invalid_preference' => 'Ogni preferenza deve essere un valore booleano.',
    ],

    'banner' => [
        'title' => 'Preferenze cookie',
        'message' => 'Usiamo i cookie per far funzionare il sito e, con il tuo consenso, per misurarlo e migliorarlo. Puoi accettare tutti i cookie, rifiutare quelli opzionali o scegliere le categorie.',
        'accept_all' => 'Accetta tutti',
        'reject_optional' => 'Rifiuta opzionali',
        'customize' => 'Personalizza',
        'hide_details' => 'Nascondi',
        'save' => 'Salva preferenze',
        'privacy' => 'Informativa privacy',
        'cookie_policy' => 'Cookie policy',
        'error' => 'Impossibile salvare le preferenze. Riprova.',
        'reopen' => 'Preferenze cookie',
        'categories' => [
            'necessary' => [
                'label' => 'Necessari',
                'description' => 'Obbligatori per sicurezza, memorizzazione del consenso e funzioni di base. Sempre attivi.',
            ],
            'preferences' => [
                'label' => 'Preferenze',
                'description' => 'Memorizzano scelte come lingua o impostazioni di visualizzazione.',
            ],
            'analytics' => [
                'label' => 'Analitica',
                'description' => 'Aiutano a capire come i visitatori usano il sito per migliorarlo.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Usati per contenuti personalizzati e misurazione delle campagne.',
            ],
        ],
    ],
];
