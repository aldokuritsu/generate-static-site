<?php
/*
Plugin Name: Générateur de Site HTML Statique
Description: Génère une version statique complète du site WordPress.
Version: 1.2
Author: Aldokai
*/

// Définition d'une constante pour le chemin du plugin, utilisée pour inclure facilement d'autres fichiers du plugin.
define('GSS_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Inclusion des fichiers nécessaires pour le fonctionnement du plugin.
// admin-page.php : Gère l'interface d'administration pour la génération de pages HTML.
require_once GSS_PLUGIN_DIR . 'includes/admin-page.php';
// settings.php : Enregistre les options et réglages pour la génération HTML.
require_once GSS_PLUGIN_DIR . 'includes/settings.php';
// helpers.php : Contient des fonctions utilitaires pour le nettoyage des URLs, le téléchargement des ressources, etc.
require_once GSS_PLUGIN_DIR . 'includes/helpers.php';
// generator.php : Contient la logique principale pour parcourir et générer les pages HTML.
require_once GSS_PLUGIN_DIR . 'includes/generator.php';

// Fonction d'initialisation du plugin, qui configure le menu d'administration.
function gss_init_plugin() {
    ajouter_menu_admin_html(); // Ajoute le menu d'administration pour la génération HTML.
}

// Hook pour ajouter le menu d'administration quand l'interface d'administration est chargée.
add_action('admin_menu', 'ajouter_menu_admin_html');

// Hook pour enregistrer les réglages lors de l'initialisation de l'interface d'administration.
add_action('admin_init', 'enregistrer_generation_html_settings');

// Hook pour afficher les messages d'erreur et de succès dans l'interface d'administration.
add_action('admin_notices', 'html_admin_notices');
