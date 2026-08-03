<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Impostazioni',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Impostazioni Vcookiebar',
    ],

    'sections' => [
        'general' => 'Generale',
        'policies' => 'Link alle policy',
        'policies_help' => 'Opzionali. Pagina interna, path del sito o URL esterno (come i link dei pulsanti Voodbuilder, se disponibile).',
        'visible' => 'Categorie da mostrare',
        'visible_help' => 'Scegli quali categorie opzionali compaiono in Personalizza. I necessari restano sempre.',
        'defaults' => 'Preferenze predefinite',
        'defaults_help' => 'Applicate prima della scelta del visitatore. I cookie necessari restano attivi.',
        'appearance' => 'Aspetto',
        'appearance_help' => 'Posizione, tema e colori. Con Voodbuilder, “Tema Voodflow” segue automaticamente la palette light/dark del sito.',
    ],

    'fields' => [
        'enabled' => 'Abilita cookie bar',
        'enabled_help' => 'Se disattivato, banner pubblico, endpoint e runtime non rispondono.',
        'privacy_policy_url' => 'URL privacy policy',
        'privacy_policy' => 'Privacy policy',
        'cookie_policy' => 'Cookie policy',
        'consent_cookie' => 'Nome cookie di consenso',
        'category_necessary' => 'Necessari',
        'category_preferences' => 'Preferenze',
        'category_analytics' => 'Analitici',
        'category_marketing' => 'Marketing',
        'link_type_url' => 'URL esterno',
        'link_type_path' => 'Path del sito',
        'link_target' => 'Link',
        'link_target_url_help' => 'URL assoluto (https://…).',
        'link_target_path_help' => 'Path su questo sito, es. /privacy.',
        'placement' => 'Posizione banner',
        'placement_bottom' => 'Basso (larghezza piena)',
        'placement_bottom_right' => 'Basso a destra (flottante)',
        'placement_bottom_left' => 'Basso a sinistra (flottante)',
        'placement_top' => 'Alto (larghezza piena)',
        'theme' => 'Tema colori',
        'theme_voodflow' => 'Tema Voodflow (light/dark automatico)',
        'theme_auto' => 'Auto di sistema (prefers-color-scheme)',
        'theme_custom' => 'Colori personalizzati',
        'theme_help' => 'I colori personalizzati valgono solo con “Colori personalizzati”.',
        'reopen_icon' => 'Mostra icona per riaprire le preferenze',
        'reopen_icon_help' => 'Controllo discreto dopo il consenso per modificare le preferenze.',
        'color_panel_bg' => 'Sfondo pannello',
        'color_text' => 'Testo',
        'color_muted' => 'Testo secondario',
        'color_button_primary_bg' => 'Sfondo pulsante primario',
        'color_button_primary_text' => 'Testo pulsante primario',
        'color_button_bg' => 'Sfondo pulsante secondario',
        'color_button_text' => 'Testo pulsante secondario',
    ],

    'actions' => [
        'save' => 'Salva impostazioni',
    ],

    'notifications' => [
        'settings_saved' => 'Impostazioni Vcookiebar salvate.',
    ],
];
