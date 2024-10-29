<?php

// Enregistre les réglages pour le plugin dans la base de données WordPress.
function enregistrer_generation_html_settings() {
    // Enregistre un paramètre pour le groupe 'generation-html-settings'.
    register_setting('generation-html-settings', 'generation_html', array(
        // Utilise une fonction de rappel pour nettoyer les données enregistrées dans ce paramètre.
        'sanitize_callback' => 'sanitize_text_field',
        // Définit une valeur par défaut pour le paramètre.
        'default' => ''
    ));
    // La fonction `register_setting` crée l'entrée dans la base de données et gère le stockage de ce paramètre.
}

// Affiche les messages de notification dans le panneau d'administration de WordPress.
function html_admin_notices() {
    // Affiche les erreurs et messages liés au groupe de paramètres 'generation-html-messages'.
    // Ces messages peuvent inclure des notifications de succès, d'erreurs, ou d'avertissements 
    // liés à la génération des pages HTML statiques (par exemple, succès de la génération, échec, etc.).
    settings_errors('generation-html-messages');
    // La fonction `settings_errors` récupère et affiche tous les messages ajoutés via `add_settings_error`.
}
