<?php

// Supprime tous les fichiers et dossiers dans un répertoire donné.
function vider_dossier($dossier) {
    // Utilise un itérateur pour parcourir tous les fichiers et dossiers récursivement.
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dossier, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    // Parcourt chaque fichier et dossier, en les supprimant un par un.
    foreach ($files as $fileinfo) {
        // Détermine s'il s'agit d'un fichier ou d'un dossier et utilise la fonction appropriée.
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath()); // Supprime le fichier ou le dossier.
    }
}

// Nettoie une URL en supprimant les paramètres de requête, et en validant le format.
function myplugin_clean_url($url) {
    // Parse l'URL pour obtenir ses différentes parties (schéma, hôte, chemin, etc.).
    $parsed_url = parse_url($url);
    // Si l'URL n'a pas de schéma (http/https) ou d'hôte (nom de domaine), retourne une chaîne vide.
    if (empty($parsed_url['scheme']) || empty($parsed_url['host'])) {
        return '';
    }
    // Retourne l'URL nettoyée et validée en utilisant la fonction WordPress pour plus de sécurité.
    return esc_url($url);
}

// Récupère le HTML d'une URL spécifique et appelle la fonction pour traiter les ressources.
function get_html_from_url($url, $dossier) {
    // Envoie une requête HTTP GET vers l'URL.
    $response = wp_remote_get($url);
    // Si la requête échoue, retourne 'false' pour indiquer une erreur.
    if (is_wp_error($response)) {
        return false;
    }
    // Récupère le contenu HTML de la réponse.
    $html = wp_remote_retrieve_body($response);

    // Appelle la fonction pour télécharger et réécrire les chemins des ressources dans le HTML.
    return download_and_rewrite_resources($html, $url, $dossier);
}

// Télécharge les ressources externes (CSS, JS, images) et réécrit leurs URLs dans le HTML.
function download_and_rewrite_resources($html, $base_url, $dossier) {
    $doc = new DOMDocument();
    @$doc->loadHTML($html); // Charge le HTML dans un objet DOM pour le manipuler.

    // Définit les dossiers où les ressources seront enregistrées.
    $cssDir = $dossier . 'css/';
    $jsDir = $dossier . 'js/';
    $imgDir = $dossier . 'img/';

    // Traite les balises <link> pour les fichiers CSS.
    $links = $doc->getElementsByTagName('link');
    foreach ($links as $link) {
        // Si le lien est une feuille de style, télécharge et sauvegarde le fichier CSS.
        if ($link->getAttribute('rel') == 'stylesheet') {
            $href = $link->getAttribute('href');
            $clean_href = myplugin_clean_url($href); // Nettoie l'URL.
            $response = wp_remote_get($clean_href);
            if (is_wp_error($response)) continue;
            $css_content = wp_remote_retrieve_body($response);
            // Génére un nom de fichier propre et enregistre le fichier dans le dossier CSS.
            $css_filename = sanitize_file_name(basename(parse_url($clean_href, PHP_URL_PATH)));
            file_put_contents($cssDir . $css_filename, $css_content);
            // Met à jour le chemin du CSS dans le HTML pour pointer vers la version locale.
            $link->setAttribute('href', './css/' . $css_filename);
        }
    }

    // Traite les balises <script> pour les fichiers JavaScript.
    $scripts = $doc->getElementsByTagName('script');
    foreach ($scripts as $script) {
        $src = $script->getAttribute('src');
        if ($src) {
            $clean_src = myplugin_clean_url($src); // Nettoie l'URL du script.
            $response = wp_remote_get($clean_src);
            if (is_wp_error($response)) continue;
            $js_content = wp_remote_retrieve_body($response);
            // Génére un nom de fichier propre et enregistre le fichier dans le dossier JS.
            $js_filename = sanitize_file_name(basename(parse_url($clean_src, PHP_URL_PATH)));
            file_put_contents($jsDir . $js_filename, $js_content);
            // Met à jour le chemin du script dans le HTML pour pointer vers la version locale.
            $script->setAttribute('src', './js/' . $js_filename);
        }
    }

    // Traite les balises <img> pour les images.
    $images = $doc->getElementsByTagName('img');
    foreach ($images as $img) {
        $src = $img->getAttribute('src');
        $clean_src = myplugin_clean_url($src); // Nettoie l'URL de l'image.

        $response = wp_remote_get($clean_src);
        if (is_wp_error($response)) continue;
        
        $img_content = wp_remote_retrieve_body($response);
        // Génére un nom de fichier propre et enregistre l'image dans le dossier img.
        $img_filename = sanitize_file_name(basename(parse_url($clean_src, PHP_URL_PATH)));
        
        file_put_contents($imgDir . $img_filename, $img_content); // Sauvegarde l'image dans le dossier img
        
        // Met à jour l'attribut src de l'image pour pointer vers le chemin local dans le HTML.
        $img->setAttribute('src', './img/' . $img_filename);
    }

    // Retourne le HTML modifié avec les nouveaux chemins des ressources.
    return $doc->saveHTML();
}
